<?php

use Fuel\Core\Debug;
use Fuel\Core\Input;

class Controller_Mem_Search extends Controller_Base
{
	protected $pid = "pid";

	public function action_index()
	{
		return Response::redirect('/');
	}

	public function action_record()
	{
		$types = array('1' => '号码', '2' => '单双');
		$winType = array('0' => '未开奖','1' => '中奖', '2' => '未中奖');
		$singleOrDouble = array('0' => '双', '1' => '单');

		
		$data = array();
		$user_id = Auth::get_user_id();
		$start_get = Input::get('start', date('Y-m-d',time())." 00:00:00");
		$end_get = Input::get('end', date('Y-m-d')." 23:59:59");


		$start = strtotime($start_get);
		$end = strtotime($end_get);
		
		$data['betdata'] = Model_Bet::find_bet_userId($user_id[1], $start, $end);

		foreach($data['betdata'] as $bet)
		{
			$bet->created_at = Date::forge($bet->created_at)->format("%Y-%m-%d %H:%M:%S");
			$bet->bet_number = $bet->type == 1?  $bet->bet_number : $singleOrDouble[$bet->bet_number];
			$bet->type = $types[$bet->type];
			$bet->isWin = $winType[$bet->isWin];
		}			
		
		// echo \DB::last_query();
		// Debug::dump($start_get, $end_get, $start, $end);exit();
		return View::forge('mem/record', $data);
	}

	public function action_deal()
	{
		$types = array('1' => '下注', '2' => '派彩');

		$user_id = Auth::get_user_id();
		$start_get = Input::get('start', date('Y-m-d',time())." 00:00:00");
		$end_get = Input::get('end', date('Y-m-d')." 23:59:59");

		$start = strtotime($start_get);
		$end = strtotime($end_get);

		$data = array();

		$data['dealdata'] = Model_Amount_Log::find_logs_userId($user_id[1], $start, $end);
		
		
		foreach($data['dealdata'] as $bet)
		{
			$bet->created_at = Date::forge($bet->created_at)->format("%Y-%m-%d %H:%M:%S");
			$bet->type = $types[$bet->type];
		}	
		// Debug::dump($data['dealdata']);exit();
		return View::forge('mem/deal', $data);
	}
	
	public function action_period()
	{
		$types = array('0' => '开盘中', '1' => '关盘');

		$start_get = Input::get('start', date('Y-m-d',time())." 00:00:00");
		$end_get = Input::get('end', date('Y-m-d')." 23:59:59");

		$start = strtotime($start_get);
		$end = strtotime($end_get);

		$data = array();

		$data['pdata'] = Model_Period::find_logs($start, $end);
		foreach($data['pdata'] as $bet)
		{
			$bet->created_at = Date::forge($bet->created_at)->format("%Y-%m-%d %H:%M:%S");
			$bet->isClose = $types[$bet->isClose];
		}	

		return View::forge('mem/period', $data);
	}
}