<?php

use ___PHPSTORM_HELPERS\object;
use Fuel\Core\Debug;

class Controller_Game_Ulp extends Controller_Base
{
    public function get_index()
    {
        $pid = Config::get('myconfig.period.pid');
        $stop_time = Config::get('myconfig.period.stop_time');
        $wait_time = Config::get('myconfig.period.wait_time');
        $periodList = $this->redis->get($pid);
        $period = json_decode($periodList);
        $data = array(
            'period'       => '',
            'time'         => '',
            'max'          => '',
            'min'          => '',
            'total'        => 40,
            'round_number' => array(),
            'rate'         => array('n' => 0, 's' => 0, 'd' => 0),
            'userid'       => Auth::get('id')
        );

        $data['rate'] = (object)$data['rate'];
        if ($period != null) {
            $data['period'] = $period->pid;
            $data['time'] = $period->time;
            $data['max'] = $period->max;
            $data['min'] = $period->min;
            $data['rate'] = $period->rate;
            $data['round_number'] = $period->round_number;
            if (count($period->round_number) > floor($period->totalTime / ($stop_time + $wait_time))) {
                array_pop($data['round_number']);
            }

        }

//		return View::forge('game/ulp', $data);
        return Presenter::forge('game/ulp', 'view', null, View::forge('game/ulp_ws', $data));
    }
}
