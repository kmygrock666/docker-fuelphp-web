<?php

namespace Fuel\Tasks;
use Fuel\Core\Redis_Db;
use Model_Period;
use Model_Round;
use game\play\NumberPlay;
use game\play\SDPlay;
use Fuel\Core\Autoloader;

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
        // var_dump($periodList);
        if($periodList == null)
        {
            $newPeriod = Timer::producePeriod();
            $openWin = Timer::getNumber(1,40);
            Model_Period::insert_Period($newPeriod, $openWin);
            $rate = Timer::getRateTable(1,40);
            $r = Model_Round::insert_Round($newPeriod, 40, $rate);
            $period_redis = Timer::getFormate($newPeriod, $openWin, $r->id, $rate);
            //start timing

            $redis->set(Timer::$pid,$period_redis);
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

    private static function getFormate($period, $pwd, $r, $rate)
    {
        return json_encode(array('pid' => $period,'close' => false,'time' => 1, 'totalTime' => 1, 
        'pwd' => $pwd , 'next_num' => 0, 'round' => $r, 'min' => 1, 'max' => 40, 'rate' => $rate));
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
                if(Timer::sendOut($val))
                {
                    $val->close = true;
                    $val->time = 0;
                }
                $val = Timer::getNewNumber($val);
            }
            else if ($val->time == 70)
            {
                $val->time = 0;
                $rate = Timer::getRateTable($val->min, $val->max);
                $r = Model_Round::insert_Round($val->pid, $val->next_num, $rate);
                $val->round = $r->id;
                $val->rate = $rate;
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

        if($game_number->getResult())
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
}

/* End of file tasks/robots.php */
