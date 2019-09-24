<?php

use game\play\NumberPlay;

class Controller_Api_Games extends Controller_Rest
{
    protected $pid = "pid";

    public function get_st()
    {
        $redis = Redis_Db::instance();
        $periodList = $redis->get($this->pid);

        if($periodList == null) return $this->response(array('code' => '1', 'message' => 'failure'));

        $period = json_decode($periodList);

        $data = array(
            'pid' => $period->pid,
            'close' => $period->close,
            'time' => $period->time,
            'totalTime' => $period->totalTime,
            'min' => $period->min,
            'max' => $period->max,
            'pwd' => '',
        );

        if($period->close)
        {
            $data['pwd'] = $period->pwd;
        }

        
        return $this->response(array(
            'code' => "0",
            'data' => $data,
        ));
        
    }

    
}