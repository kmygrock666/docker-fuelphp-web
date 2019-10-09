<?php

class Controller_Api_Games extends Controller_Apibase
{
    public function get_st()
    {
		$pid = Config::get('myconfig.period.pid');
		$stop_time = Config::get('myconfig.period.stop_time');
		$wait_time = Config::get('myconfig.period.wait_time');
        $periodList = $this->redis->get($pid);

        if($periodList == null) return $this->response(array('code' => '1', 'message' => Lang::get('error.ER6')));

        $period = json_decode($periodList);

        $data = array(
            'pid' => $period->pid,
            'close' => $period->close,
            'time' => $period->time,
            // 'totalTime' => $period->totalTime,
            'min' => $period->min,
            'max' => $period->max,
            'rate' => $period->rate,
            'status' => 'bet',
            'round_number' => $period->round_number,
        );

        if($period->close)
        {
            $data['status'] = "close";
            $data['pwd'] = $period->pwd;
            $data['time'] = $wait_time - ($period->time % $stop_time);
        }
        else
        {
            if($period->time <= $stop_time)
            {
                $data['status'] = "bet";
                $data['time'] = $stop_time - $period->time;
            }
            else if($period->time <= ($stop_time + $wait_time))
            {
                $data['status'] = "stop";
                $data['time'] = $wait_time - ($period->time % $stop_time);
            }
        }

        
        return $this->response(array(
            'code' => "0",
            'data' => $data,
        ));
        
    }

    public function get_lastPeriod()
    {
        $data = array();
        $period = Model_Period::find_period_lastest(true);
        if($period == null) return $this->response(array('code' => '1', 'message' => Lang::get('error.ER6')));
        $data['pid'] = $period->pid;
        $data['open'] = $period->open_win;
        $data['round'] = array();
        foreach($period->round as $r)
        {
            array_push($data['round'], $r->open_win);
        }

        return $this->response(array(
            'code' => "0",
            'data' => $data,
        ));
    }
}