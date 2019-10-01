<?php

namespace Fuel\Tasks;

use ___PHPSTORM_HELPERS\object;
use Fuel\Core\Redis_Db;
use Model_Period;
use Model_Round;
use game\play\NumberPlay;
use game\play\SDPlay;
use Fuel\Core\Autoloader;
use Fuel\Core\Config;
use Fuel\Core\Debug;
use Fuel\Core\Date;
use Fuel\Core\DB;
use game\play\UltimatPassword;

class Timer
{
    protected static $pid;
    protected static $wait_time;
    protected static $stop_time;
    protected static $period_deadline;

	public static function run($t = 60)
	{
        Timer::$pid = Config::get('myconfig.period.pid');
        Timer::$wait_time = Config::get('myconfig.period.wait_time');
        Timer::$stop_time = Config::get('myconfig.period.stop_time');
        Timer::$period_deadline = Config::get('myconfig.period.period_deadline');
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
                $r = Model_Round::insert_Round($pid, 1, $rate);
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
        $id = Model_Period::find_period_maxid();
        if( ! $id) $id = 1;
        else $id++;
        $auto_id = str_pad($id, 4, "0", STR_PAD_LEFT);
        return date("Y").date("m").date("d").$auto_id;
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
        'pwd' => $pwd, 'round' => $r, 'round_number' => array(), 'next' => 0, 'min' => 1, 'max' => 40, 'rate' => $rate);
    }

    private static function condition($val)
    {
        if ($val->close)
        {
            if($val->time == Timer::$wait_time)
            {
				Model_Period::save_period_status($val->pid_);
                return null;
            }
        }
        else
        {
            if ($val->totalTime == Timer::$period_deadline) //每一期限制時間
            {
                $val->close = true;
                $val->time = 0;
            }
            else if($val->time == Timer::$stop_time) //停止下注
            {
                // 開下一回區間
                $next_range_number = Timer::getNumber($val->min, $val->max);
                Model_Round::save_status($val->round, $next_range_number);
                $val->next = $next_range_number;
                array_push($val->round_number, $val->next);

                //結算
                if(Timer::sendOut($val, $next_range_number))
                {
                    $val->close = true;
                    $val->time = 0;
                }
            }
            else if ($val->time == (Timer::$wait_time + Timer::$stop_time)) 
            {
                $val->time = 0;
                //refresh redis round
                Timer::getNewNumber($val, $val->next);
                //insert next new round
                if( ! $val->close)
                {
                    $rate = Timer::getRateTable($val->min, $val->max);
                    $r = Model_Round::insert_Round($val->pid_, 0, $rate);
                    $val->round = $r->id;
                    $val->rate = $rate;
                }
            }
        }

		$val->time ++;
        $val->totalTime ++;
        return $val;
    }

    private static function getNewNumber(&$val, $number)
    {
        if($number == $val->pwd)
        {
            $val->close = true;
            $val->time = 0;
        }
        else if($number > $val->pwd)
        {
            $val->max = $number - 1;
        }
        else
        {
            $val->min = $number + 1;
        }

        if(($val->max - $val->min) == 0)
        {
            $val->close = true;
            $val->time = 0;
        }
        return $val;
    }
    
    private static function sendOut($val, $number)
    {
        $ultimatPasswordFactory = UltimatPassword::getInstance();
        $ultimatPasswordFactory->create_play($val->pid, $val->round, $number, $val->max, $val->min);
        $ultimatPasswordFactory->settle('SDP');
        $response = $ultimatPasswordFactory->settle('NP');
        // $game_sD = new SDPlay($val->pid, $val->round, $number, $val->max, $val->min);
        // $game_sD->getResult();

        // Autoloader::add_class('game\play\NumberPlay', APPPATH.'game/play/numberPlay.php');
        // $game_number = new NumberPlay($val->pid, $val->round, $val->pwd, ($val->max - $val->min + 1));
        // $response = $game_number->getResult();
        
        Model_Round::save_settle_status($val->round);
        // Debug::dump(Date::forge($round->updated_at)->format("%Y-%m-%d %H:%M:%S"));exit();
        if($response)
        {
            return true;
        }
        return false;
    }

    private static function getRateTable($min, $max)
    {
        $ultimatPasswordFactory = UltimatPassword::getInstance();
        $ultimatPasswordFactory->create_play(0, 0, 0, $max, $min);
        $number = $ultimatPasswordFactory->getRate('NP');
        $sdp = $ultimatPasswordFactory->getRate('SDP');
        $single = $sdp[1];
        $double = $sdp[0];
        // $game_sD = new SDPlay(0, 0, 0, $max, $min);
        // $game_number = new NumberPlay(0, 0, 0, ($max - $min + 1));
        // $game_sD->setSelected(1);
        // $single = $game_sD->getRate();
        // $game_sD->setSelected(0);
        // $double = $game_sD->getRate();
        // $number = $game_number->getRate();
        return array('n' => $number, 's' => $single, 'd' => $double);
    }

    private static function getPeriodByDB(&$period_redis)
    {
        $periodData = Model_Period::find_period_lastest(false);
        if($periodData == null) return false;

        $round = Model_Round::find_by_period($periodData->id);
        if($round == null)
        {
            $rate = Timer::getRateTable(1,40);
            $r = Model_Round::insert_Round($periodData->id, 1, $rate);
            $period_redis = Timer::getFormate($periodData->id, $periodData->pid, $periodData->open_win, $r->id, $rate);
        }
        else
        {
            ksort($round);
            $round = array_values($round);
            $round_count = count($round);
            // Debug::dump($round);exit();
            if($round[$round_count - 1]->is_settle == true)
            {
                Timer::settle($periodData, $round);
                $round[$round_count - 1]->is_settle = 2;
            }

            // if($round_count == 3 and $round[$round_count - 1]->is_settle == 2) return false;
            // Debug::dump($round, $round_count);exit();
            $period_redis = (object) Timer::getFormate($periodData->id, $periodData->pid, $periodData->open_win, $round[$round_count-1]->id, array());
            $time = 0;
            
            foreach($round as $r)
            {
                if($r->is_settle == 2) $time++;
                $period_redis->rate = json_decode($r->rate);
                Timer::getNewNumber($period_redis, $r->open_win);
            }
            //檢查期數是否已結束
            if($period_redis->close) return false;

            if($time > 0){
                //檢查回合是否已結算，建立新的回合
                if($round[$round_count-1]->is_settle == 2)
                {
                    $rate = Timer::getRateTable($period_redis->min, $period_redis->max);
                    $r = Model_Round::insert_Round($period_redis->pid_, 0, $rate);
                    $period_redis->round = $r->id;
                    $period_redis->rate = $rate;
                }
                $period_redis->totalTime = $time * (Timer::$wait_time + Timer::$stop_time);
            }
        }

        return true;
    }

    private static function settle($periodData, $round)
    {
        $r_count = count($round);
        $period_value = (object) Timer::getFormate($periodData->id, $periodData->pid, $periodData->open_win, $round[$r_count - 1]->id, array());
        foreach($round as $r)
        {
            $period_value->rate = json_decode($r->rate);
            Timer::getNewNumber($period_value, $r->open_win);
        }

        Timer::sendOut($period_value, $round[$r_count - 1]->open_win);
        return true;
    }
}

/* End of file tasks/robots.php */
