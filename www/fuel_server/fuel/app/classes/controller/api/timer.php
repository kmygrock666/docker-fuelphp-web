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
        if($periodList == null) return $this->response(array('code' => '1', 'message' => 'no data'));
        $redis->del($this->pid);
        return $this->response(array('code' => '0', 'message' => 'Success delete'));
        
    }

    private function closePeriod($pid)
	{		
		$period = Model_Period::find_by_pid($pid);
		$period->isClose = true;
		return $period->save();
    }
}