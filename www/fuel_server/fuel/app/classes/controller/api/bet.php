<?php

use Auth\Auth;
use Fuel\Core\Input;
use game\play\Deal;

class Controller_Api_Bet extends Controller_Rest
{
    protected $pid = "pid";

    public function get_send()
    {
        
        $redis = Redis_Db::instance();
        $periodList = $redis->get($this->pid);

        if($periodList == null) return $this->response(array('code' => '5', 'message' => 'period not open'));
        
        $period = json_decode($periodList);

        $pid = $period->pid;
        $rid = $period->round;
        $user_bet = Input::get('b', null);
        $type = Input::get('t', null);
        $amount = Input::get('m', null);
        $user_id = Auth::get_user_id();

        if($user_bet == null || $type == null || $amount == null) return $this->response(array('code' => '1', 'message' => 'post has error'));

        if($period->close == false && $period->time <= 60)
        {
            $params_vailure = false;

            switch($type)
            {
                case 1: //number
                    if($user_bet >= $period->min and $user_bet <= $period->max)
                    {
                        $params_vailure = true;
                    }
                    break;
                case 2: //single Double
                    if($user_bet == 's') $user_bet = 1;
                    if($user_bet == 'd') $user_bet = 0;
                    $params_vailure = true;
                    break;
            }

            if($params_vailure)
            {
                Autoloader::add_class('game\play\Deal', APPPATH.'game/play/deal.php');
                $deal = new Deal();
                $deal_result = $deal->do_deal($amount, $user_id[1], $user_bet, $rid, $pid, $type);
                if($deal_result['code'])
                    return $this->response(array('code' => '3', 'message' => $deal_result['message']));
            }
        }
        else
            return $this->response(array('code' => '4', 'message' => 'Prohibited bet, not within timing'));
        
        return $this->response(array(
            'code' => "0",
            'message' => 'success',
            'data' => array(),
        ));
        
    }

    
}