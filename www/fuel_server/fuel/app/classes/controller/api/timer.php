<?php

use game\play\NumberPlay;

class Controller_Api_Timer extends Controller_Apibase
{
    protected $pid = "pid";

    public function get_001()
    {
        //TODO if db not exist
        $redis = Redis_Db::instance();
        // $redis->del($this->pid);
        $periodList = $redis->get($this->pid);
        var_dump($periodList);echo "<br>";
    }

    public function get_close()
    {
        $redis = Redis_Db::instance();
        $periodList = $redis->get($this->pid);
        $period = json_decode($periodList);
        if($this->closePeriod($period->pid))
        {
            $redis->del($this->pid);
        }
    }

    private function closePeriod($pid)
	{		
		$period = Model_Period::find_by_pid($pid);
		$period->isClose = true;
		return $period->save();
    }
}