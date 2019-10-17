<?php

namespace Fuel\Tasks;

use ___PHPSTORM_HELPERS\object;
use Fuel\Core\Redis_Db;
use Model_Period;
use Model_Round;
use Fuel\Core\Autoloader;
use Fuel\Core\Config;
use Fuel\Core\Debug;
use Fuel\Core\Date;
use Fuel\Core\DB;
use game\play\UltimatPassword;
use game\ws\WsPublish;

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
                $openWin = Timer::getUltimatetNumber(1, 40);
                $pid = Model_Period::insert_Period($newPeriod, $openWin);
                $rate = Timer::getRateTable(1, 40);
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
//                WsPublish::send("up", );
                $redis->set(Timer::$pid, json_encode($period));
            }
        }
    }
    /**
     * 產生期數規則
     */
    private static function producePeriod()
    {
        $id = Model_Period::find_period_maxid();
        if( ! $id) $id = 1;
        else $id++;
        $auto_id = str_pad($id, 4, "0", STR_PAD_LEFT);
        return date("Y").date("m").date("d").$auto_id;
    }
    /**
     * 產生每回合獎號
     */
    private static function getUltimatetNumber($min, $max)
    {
        return mt_rand($min, $max);
    }
    /**
     * 初始化
     */
    private static function getFormate($pid_, $period, $pwd, $r, $rate)
    {
        return array('pid_' => $pid_,  //期數id
                     'pid' => $period, //期數
                     'close' => false, //期數開關盤
                     'time' => 1, //目前秒數
                     'totalTime' => 1,  //總秒數
                     'pwd' => $pwd, //終極密碼
                     'round' => $r,  //回合數id
                     'round_number' => array(), //每回合獎號
                     'next' => 0,  //該回合開出號碼
                     'min' => 1,
                     'max' => 40,
                     'rate' => $rate); // 該回合賠率
    }
    /**
     * 遊戲規則
     * 每局最多 3分30秒
     * 有人中獎(選號中獎)或僅剩一球即結束該局
     * 結束後10秒再開新局，可下注60秒
     */
    private static function condition($val)
    {
        if ($val->close)
        {   //該局結束，等n秒後開新局
            if($val->time == Timer::$wait_time)
            {
				Model_Period::save_period_status($val->pid_);
                return null;
            }
        }
        else
        {   //停止下注
            if($val->time == Timer::$stop_time)
            {
                // 開出新回合號碼
                $next_range_number = Timer::getUltimatetNumber($val->min, $val->max);
                Model_Round::save_status($val->round, $next_range_number);

                $val->next = $next_range_number;
                array_push($val->round_number, $val->next);

                //結算，若有人中終極密碼，開下一盤
                if(Timer::sendOut($val, $next_range_number))
                {
                    $val->close = true;
                    $val->time = 0;
                }
            }//等n秒後，開下一局
            else if ($val->time == (Timer::$wait_time + Timer::$stop_time)) 
            {
                $val->time = 0;
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
        // 系統取號 ＝＝ 終極號碼
        if($number == $val->pwd)
        {
            $val->close = true;
            $val->time = 0;
        }
        if($number > $val->pwd)
        {
            $val->max = $number - 1;
        }
        else
        {
            $val->min = $number + 1;
        }

        // 剩餘一個號碼
        if(($val->max - $val->min) == 0)
        {
            $val->close = true;
            $val->time = 0;
        }

        //每一期限制時間
        if ($val->totalTime == (Timer::$period_deadline - Timer::$wait_time))
        {
            $val->close = true;
            $val->time = 0;
        }
        return $val;
    }
    
    private static function sendOut(&$val, $number)
    {
        $ultimatPasswordFactory = UltimatPassword::getInstance();
        $ultimatPasswordFactory->create_play($val->pid, $val->round, $val->pwd, $val->max, $val->min, $number);
        $sdp_response = $ultimatPasswordFactory->settle('SDP', true);
        //refresh redis round
        Timer::getNewNumber($val, $number);

        if(is_bool($sdp_response))
        {
            //不判斷輸贏 ()
            if($sdp_response and ! $val->close)
                $np_response = $ultimatPasswordFactory->settle('NP', false);
            else//判斷輸贏
                $np_response = $ultimatPasswordFactory->settle('NP', true);

            if (is_bool($np_response))
            {
                Model_Round::save_settle_status($val->round);
                if ($np_response) return true;
            }

        }

        // Debug::dump(Date::forge($round->updated_at)->format("%Y-%m-%d %H:%M:%S"));exit();

        return false;
    }

    private static function getRateTable($min, $max)
    {
        $ultimatPasswordFactory = UltimatPassword::getInstance();
        $ultimatPasswordFactory->create_play(0, 0, 0, $max, $min, 0);
        $number = $ultimatPasswordFactory->getRate('NP');
        $sdp = $ultimatPasswordFactory->getRate('SDP');
        $single = $sdp[1];
        $double = $sdp[0];
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
                $response = Timer::settle($periodData, $round);
                //玩家中終極號碼，開新局
                if($response) 
                {
                    Model_Period::save_period_status($periodData->id);
                    return false; 
                }
                $round[$round_count - 1]->is_settle = 2;
            }
            // Debug::dump($round, $round_count);exit();
            $period_redis = (object) Timer::getFormate($periodData->id, $periodData->pid, $periodData->open_win, $round[$round_count-1]->id, array());
            $time = 0;
            
            foreach($round as $r)
            {
                if($r->is_settle == 2) 
                {
                    $time++;
                    Timer::getNewNumber($period_redis, $r->open_win);
                    array_push($period_redis->round_number, $r->open_win);
                }
                $period_redis->rate = json_decode($r->rate);
                
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

        $response = Timer::sendOut($period_value, $round[$r_count - 1]->open_win);
        return $response;
    }
}

/* End of file tasks/robots.php */
