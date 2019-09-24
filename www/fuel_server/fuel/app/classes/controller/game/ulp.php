<?php

class Controller_Game_Ulp extends Controller_Base
{
	protected $pid = "pid";

	public function action_index()
	{
		// $widget = Request::forge('base/template')->execute("development");
		// echo $widget;
		$redis = Redis_Db::instance();
		$periodList = $redis->get($this->pid);
		$period = json_decode($periodList);
		$data = array();
		$data['period'] = $period->pid;
		$data['time'] = $period->time;
		$data['max'] = $period->max;
		$data['min'] = $period->min;
		$data['total'] = 40;
		return View::forge('game/ulp', $data);
	}
}
