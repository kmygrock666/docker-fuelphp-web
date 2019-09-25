<?php

class Controller_Game_Ulp extends Controller_Base
{
	protected $pid = "pid";

	public function action_index()
	{
		$redis = Redis_Db::instance();
		$periodList = $redis->get($this->pid);
		$period = json_decode($periodList);
		$data = array('period' => '','time' => '','max' => '','min' => '','total' => 40, 'rate' => array('n'=> 0, 's'=> 0, 'd' => 0));
		if($period != null)
		{
			$data['period'] = $period->pid;
			$data['time'] = $period->time;
			$data['max'] = $period->max;
			$data['min'] = $period->min;
			$data['rate'] = $period->rate;
		}
		
		return View::forge('game/ulp', $data);
	}
}
