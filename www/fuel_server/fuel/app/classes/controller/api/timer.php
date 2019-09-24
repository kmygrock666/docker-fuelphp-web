<?php

use game\play\NumberPlay;

class Controller_Api_Timer extends Controller_Rest
{
    protected $pid = "pid";

    public function get_001()
    {
        //TODO if db not exist
        $redis = Redis_Db::instance();
        // $redis->del($this->pid);
        $periodList = $redis->get($this->pid);
        var_dump($periodList);echo "<br>";
        exit();
        if($periodList == null)
        {
            $newPeriod = $this->producePeriod();
            $openWin = $this->getNumber(1, 40);
            $this->insertPeriod($newPeriod, $openWin);
            $r = $this->insertRound($newPeriod, 0);
            $period_redis = $this->getFormate($newPeriod, $openWin, $r->id);

            //start timing

            $redis->set($this->pid,$period_redis);
        }
        else
        {
            $period = $this->condition(json_decode($periodList));
            if ($period == null)
            {
                $redis->del($this->pid);
            }
            else
            {
                $redis->set($this->pid, json_encode($period));
            }
        }

        return $this->response(array(
            'code' => "success",
            'data' => array(),
        ));
    }

    private function producePeriod()
    {
        return date("Y").date("m").date("d").substr(strtotime('now'),-4);
    }

    private function getNumber($min, $max)
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

    private function getFormate($period, $pwd, $r)
    {
        return json_encode(array('pid' => $period,'close' => false,'time' => 1, 'totalTime' => 1, 'pwd' => $pwd , 'round' => $r, 'min' => 0, 'max' => 40));
    }

    private function condition($val)
    {
        if ($val->close)
        {
            if($val->time == 10)
            {
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
                if($this->sendOut($val))
                {
                    $val->close = true;
                    $val->time = 0;
                }
            }
            else if ($val->time == 70)
            {
                $val->time = 0;
                $number_next = $this->getNumber($val->pwd, $val->min, $val->max);
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
                    $r = $this->insertRound($val->pid, $number_next);
                    $val->round = $r->id;
                }

            }
        }

        $val->time ++;
        $val->totalTime ++;
        return $val;
    }

    private function insertPeriod($period, $openWin)
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

    private function insertRound($period, $openWin)
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

    private function sendOut($val)
    {
        Autoloader::add_class('game\play\NumberPlay', APPPATH.'game/play/numberPlay.php');
        $game_number = new NumberPlay($val->pid, $val->round, $val->pwd, ($val->max - $val->min + 1));

        if($game_number->getResult())
        {
            return true;
        }
        return false;
    }
}