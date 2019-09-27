<?php

namespace Fuel\Tasks;

use ___PHPSTORM_HELPERS\object;
use Fuel\Core\Redis_Db;
use Model_Period;
use Model_Round;
use game\play\NumberPlay;
use game\play\SDPlay;
use Fuel\Core\Autoloader;
use Fuel\Core\Debug;
use Fuel\Core\Date;

class Timer
{
	protected static $pid = "pid";

	public static function run($t = 60)
	{
		$count = 0;
		while(true){
			$count++;
			// echo "now time is ".date('h:i:s');
			Timer::get_001();
			usleep(1000000);
			if($count == $t) break;
		}
	}

	public static function get_001()
    {
        
        $redis = Redis_Db::instance();
        $periodList = $redis->get(Timer::$pid);
        $period_redis = '';
        if($periodList == null)
        {
            //find db period
            if( ! Timer::getPeriodByDB($period_redis))
            {
                $newPeriod = Timer::producePeriod();
                $openWin = Timer::getNumber(1,40);
                $pid = Model_Period::insert_Period($newPeriod, $openWin);
                $rate = Timer::getRateTable(1,40);
                $r = Model_Round::insert_Round($pid, 40, $rate);
                $period_redis = Timer::getFormate($pid, $newPeriod, $openWin, $r->id, $rate);
            }
            
            //start timing

            $redis->set(Timer::$pid,json_encode($period_redis));
        }
        else
        {
            $period = Timer::condition(json_decode($periodList));

            if ($period == null)
            {
                $redis->del(Timer::$pid);
            }
            else
            {
                $redis->set(Timer::$pid, json_encode($period));
            }
        }
    }

    private static function producePeriod()
    {
        return date("Y").date("m").date("d").substr(strtotime('now'),-4);
    }

    private static function getNumber($min, $max)
    {

        while(true)
        {
            $number = rand($min, $max);
            if($number != $min && $number != $max)
            {
                return $number;
            }
        }
    }

    private static function getFormate($pid_, $period, $pwd, $r, $rate)
    {
        return array('pid_' => $pid_,'pid' => $period,'close' => false,'time' => 1, 'totalTime' => 1, 
        'pwd' => $pwd , 'next_num' => 0, 'round' => $r, 'min' => 1, 'max' => 40, 'rate' => $rate);
    }

    private static function condition($val)
    {
        if ($val->close)
        {
            if($val->time == 10)
            {
				Timer::closePeriod($val->pid);
                return null;
            }
        }
        else
        {
            if ($val->totalTime == (3 * 60 + 30))
            {
                $val->close = true;
                $val->time = 0;
            }
            else if($val->time == 60)
            {
                $number_next = Timer::getNumber($val->min, $val->max);
                $val->next_num = $number_next;
                //insert new round
                $min = ($number_next < $val->pwd)? $number_next : $val->min;
                $max = ($number_next > $val->pwd)? $number_next : $val->max;
                $rate = Timer::getRateTable($min, $max);
                $r = Model_Round::insert_Round($val->pid_, $val->next_num, $rate);
                //settle
                if(Timer::sendOut($val))
                {
                    $val->close = true;
                    $val->time = 0;
                }
                //refresh redis round
                Timer::getNewNumber($val);
                $val->round = $r->id;
                $val->rate = $rate;
                
            }
            else if ($val->time == 70)
            {
                $val->time = 0;
            }
        }

		$val->time ++;
        $val->totalTime ++;
        return $val;
    }

    private static function getNewNumber(&$val)
    {
        if($val->next_num == $val->pwd)
        {
            $val->close = true;
            $val->time = 0;
        }
        else if($val->next_num > $val->pwd)
        {
            $val->max = $val->next_num;
        }
        else
        {
            $val->min = $val->next_num;
        }

        if(($val->max - $val->min + 1) == 3)
        {
            $val->close = true;
            $val->time = 0;
        }
        return $val;
    }
	
	private static function closePeriod($pid)
	{		
		$period = Model_Period::find_by_pid($pid);
		$period->isClose = true;
		return $period->save();
    }
    
    private static function sendOut($val)
    {

        // Autoloader::add_class('game\play\SDPlay', APPPATH.'game/play/sDPlay.php');
        $game_sD = new SDPlay($val->pid, $val->round, $val->next_num, $val->max, $val->min);
        $game_sD->getResult();

        // Autoloader::add_class('game\play\NumberPlay', APPPATH.'game/play/numberPlay.php');
        $game_number = new NumberPlay($val->pid, $val->round, $val->pwd, ($val->max - $val->min + 1));
        $response = $game_number->getResult();
        
        $round = Model_Round::find_by_id($val->round);
        $round->isWin = true;
        $round->updated_at = strtotime('now');
        $round->save();
        // Debug::dump(Date::forge($round->updated_at)->format("%Y-%m-%d %H:%M:%S"));exit();
        if($response)
        {
            return true;
        }
        return false;
    }

    private static function getRateTable($min, $max)
    {
        $game_sD = new SDPlay(0, 0, 0, $max, $min);
        $game_number = new NumberPlay(0, 0, 0, ($max - $min + 1));
        $game_sD->setSelected(1);
        $single = $game_sD->getRate();
        $game_sD->setSelected(0);
        $double = $game_sD->getRate();
        $number = $game_number->getRate();
        return array('n' => $number, 's' => $single, 'd' => $double);
    }

    private static function getPeriodByDB(&$period_redis)
    {
        $periodData = Model_Period::find_period_lastest();
        if($periodData == null) return false;

        $round = Model_Round::find_by_period($periodData->id);
        ksort($round);
        $round = array_values($round);
        $round_count = count($round);
        
        if($round_count == 4 || $round_count > 1)
        {
            if($round[$round_count - 2]->isWin == false)
            {
                $tmp_round = $round;
                array_pop($tmp_round);
                Timer::settle($periodData, $tmp_round, $round[$round_count - 1]->openWin);
                $round[$round_count - 2]->isWin = true;
            }
        }

        if($round_count == 4) return false;
        // Debug::dump($round);exit();
        $period_redis = (object) Timer::getFormate($periodData->id, $periodData->pid, $periodData->openWin, $round[$round_count-1]->id, array());
        $time = 0;
        
        
        foreach($round as $r)
        {
            if($r->isWin) $time++;
            $period_redis->next_num = $r->openWin;
            $period_redis->rate = json_decode($r->rate);
            Timer::getNewNumber($period_redis);
        }

        if($time > 0)
            $period_redis->totalTime = $time * 70;

        return true;
    }

    private static function settle($periodData, $round, $next)
    {
        $r_count = count($round);
        $period_value = (object) Timer::getFormate($periodData->id, $periodData->pid, $periodData->openWin, $round[$r_count - 1]->id, array());
        foreach($round as $r)
        {
            $period_value->next_num = $r->openWin;
            $period_value->rate = json_decode($r->rate);
            Timer::getNewNumber($period_value);
        }
        $period_value->next_num = $next;
        Timer::sendOut($period_value);
        return true;
    }
}

/* End of file tasks/robots.php */
