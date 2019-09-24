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
            Timer::insertPeriod($newPeriod, $openWin);
            $r = Timer::insertRound($newPeriod, 40);
            $period_redis = Timer::getFormate($newPeriod, $openWin, $r->id);
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

    private static function getFormate($period, $pwd, $r)
    {
        return json_encode(array('pid' => $period,'close' => false,'time' => 1, 'totalTime' => 1, 'pwd' => $pwd , 'round' => $r, 'min' => 1, 'max' => 40));
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
                if(Timer::sendOut($val))
                {
                    $val->close = true;
                    $val->time = 0;
                }
            }
            else if ($val->time == 70)
            {
                $val->time = 0;
                $number_next = Timer::getNumber($val->min, $val->max);
                if($number_next == $val->pwd)
                {
                    $val->close = true;
                }
                else if($number_next > $val->pwd)
                {
                    $val->max = $number_next;
                }
                else
                {
                    $val->min = $number_next;
                }

                if(($val->max - $val->min + 1) == 3)
                {
                    $val->close = true;
				}
				
				if( ! $val->close)
                {
                    $r = Timer::insertRound($val->pid, $number_next);
                    $val->round = $r->id;
                }

            }
        }

		$val->time ++;
        $val->totalTime ++;
        return $val;
    }

    private static function insertPeriod($period, $openWin)
    {
        $p = Model_Period::forge(array(
            'pid' => $period,
            'openWin' => $openWin,
            'isClose' => false,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
        ));

        $result = $p->save();
        return $result;
	}

	private static function insertRound($period, $openWin)
    {
        $p = Model_Round::forge(array(
            'openWin' => $openWin,
            'isWin' => false,
            'period_id' => $period,
            'created_at' => strtotime('now'),
            'updated_at' => strtotime('now'),
        ));

        $p->save();
        return $p;
    }
	
	private static function closePeriod($pid)
	{		
		$period = Model_Period::find_by_pid($pid);
		$period->isClose = true;
		return $period->save();
    }
    
    private static function sendOut($val)
    {
        Autoloader::add_class('game\play\SDPlay', APPPATH.'game/play/sDPlay.php');
        $game_sD = new SDPlay($val->pid, $val->round, $val->pwd, $val->max, $val->min);
        $game_sD->getResult();

        Autoloader::add_class('game\play\NumberPlay', APPPATH.'game/play/numberPlay.php');
        $game_number = new NumberPlay($val->pid, $val->round, $val->pwd, ($val->max - $val->min + 1));

        if($game_number->getResult())
        {
            return true;
        }
        return false;
    }
}

/* End of file tasks/robots.php */
