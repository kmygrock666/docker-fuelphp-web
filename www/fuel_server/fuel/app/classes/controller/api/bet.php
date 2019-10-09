<?php

use Auth\Auth;
use Fuel\Core\Debug;
use Fuel\Core\Input;
use game\play\Deal;

class Controller_Api_Bet extends Controller_Apibase
{
    protected $deadline_time = 10;
    //單一下注
    public function get_send()
    {
        $pid = Config::get('myconfig.period.pid');
        $periodList = $this->redis->get($pid);

        if ($periodList == null) return $this->response(array('code' => '5', 'message' => Lang::get('error.ER6')));
        
        $period = json_decode($periodList);

        $pid = $period->pid;
        $rid = $period->round;
        $user_bet = Input::get('b', null);
        $type = Input::get('t', null);
        $amount = Input::get('m', null);
        $user_id = Auth::get_user_id();

        if ($user_bet == null || $type == null || $amount == null)
            return $this->response(array('code' => '1', 'message' => Lang::get('error.ER1')));

        if ($period->close == false && $period->time <= 60)
        {
            //限制n秒內不能連續下注
            //TODO 注單驗證待修改，可改為正規驗證
            $userdata_redis = $this->redis->get($pid.":".$user_id[1]);
            if ($userdata_redis != null)
            {
                $userdata = json_decode($userdata_redis, true);
                if (strtotime('now') - $userdata['time'] < $this->deadline_time)
                {
                    $error = 1;
                    if ($userdata['type'] == $type) $error++;
                    if ($userdata['bet'] == $user_bet) $error ++;
                    if ($userdata['amount'] == $amount) $error ++;
                    if ($error >= count($userdata)) return $this->response(array('code' => '6', 'message' => Lang::get('error.ER7')));
                }
            }
            //下注未完成，禁止在下注
            $userdata_betting_redis = $this->redis->get($pid.":".$user_id[1]."betting");
            if ($userdata_betting_redis != null)
            {
                if($userdata_betting_redis)
                    return $this->response(array('code' => '7', 'message' => Lang::get('error.ER8')));
            }

            $params_vailure = false;

            switch($type)
            {
                case 1: //number , 在範圍內
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

            if ($params_vailure)
            {
                // Autoloader::add_class('game\play\Deal', APPPATH.'game/play/deal.php');
                $deal = new Deal();
                $deal_result = $deal->bet_deal($amount, $user_id[1], $user_bet, $rid, $pid, $type);
                if($deal_result['code'])
                {
                    \Fuel\Core\Log::error('inoutdeal, line 46 error : '.$deal_result['message']);
                    return $this->response(array('code' => '3', 'message' => Lang::get('error.ER5')));
                }
            }
            else
            {
                return $this->response(array('code' => '5', 'message' => Lang::get('error.ER1')));
            }
        }
        else // Prohibited bet, not within timing
            return $this->response(array('code' => '4', 'message' => Lang::get('error.ER5')));
        
        $set_userdata = array(
            'type' => $type,
            'amount' => $amount,
            'bet' => $user_bet,
            'time' => strtotime('now'),
        );
        $this->redis->set($pid.":".$user_id[1], json_encode($set_userdata));
        $this->redis->expire($pid.":".$user_id[1], $this->deadline_time);
        
        return $this->response(array(
            'code' => "0",
            'message' => 'success',
            'data' => $deal_result['data'],
        ));
        
    }

    public function get_result()
    {
        $pid = Config::get('myconfig.period.pid');
        $periodList = $this->redis->get($pid);
        if($periodList == null) return $this->response(array('code' => 1, 'message' => Lang::get('error.ER6')));
        $period = json_decode($periodList);
        $this_round = Model_Round::find_by_open($period->pid_);
        if($this_round == null) return $this->response(array('code' => 2, 'message' => Lang::get('error.ER9')));
        
        $user_id = Auth::get_user_id();
        $bet_win = Model_Bet::find_bet_win($user_id[1], 1, $this_round->id);
        $data = array();
        foreach($bet_win as $bet)
        {
            $tmp = array();
            $tmp['type'] = $bet->type;
            $tmp['num'] = $bet->bet_number;
            $tmp['payout'] = $bet->payout;
            array_push($data, $tmp);
        }
        
        return $this->response(array(
            'code' => 0,
            'message' => 'success',
            'data' => $data,
        ));
    }
    //批量下注
    public function post_sends()
    {
        $pid = Config::get('myconfig.period.pid');
        $periodList = $this->redis->get($pid);

        if ($periodList == null) return $this->response(array('code' => '5', 'message' => Lang::get('error.ER6')));
        
        $period = json_decode($periodList);

        $pid = $period->pid;
        $rid = $period->round;
        $user_bet = Input::post('b', null);
        $type = Input::post('t', null);
        $amount = Input::post('m', null);
        $user_id = Auth::get_user_id();

        if ($user_bet == null || $type == null || $amount == null)
            return $this->response(array('code' => '1', 'message' => Lang::get('error.ER1')));

        if ($period->close == false && $period->time <= 60)
        {
            //限制n秒內不能連續下注
            $userdata_redis = $this->redis->get($pid.":".$user_id[1]);
            if ($userdata_redis != null)
            {
                $userdata = json_decode($userdata_redis, true);
                if (strtotime('now') - $userdata['time'] < $this->deadline_time)
                {
                    $error = 1;
                    if ($userdata['type'] == $type) $error++;
                    if ($userdata['bet'] == $user_bet) $error ++;
                    if ($userdata['amount'] == $amount) $error ++;
                    if ($error >= count($userdata))
                        return $this->response(array('code' => '6', 'message' => Lang::get('error.ER7')));
                }
            }

            //下注未完成，禁止再下注
            $userdata_betting_redis = $this->redis->get($pid.":".$user_id[1]."betting");
            if ($userdata_betting_redis != null)
            {
                if($userdata_betting_redis)
                    return $this->response(array('code' => '7', 'message' => Lang::get('error.ER8')));
            }
            else{
                $this->redis->set($pid.":".$user_id[1]."betting", true);
            }

            $params_vailure = true;

            switch($type)
            {
                case 1: //number
                    foreach($user_bet as $k => $b)
                    {
                        if  (is_numeric($b))
                        {
                            if($b < $period->min or $b > $period->max)
                            {
                                $params_vailure = false;
                                break;
                            }
                        }
                    }
                    
                    break;
                case 2: //single Double
                    foreach($user_bet as $k => $b)
                    {
                        if ($b == 's') $user_bet[$k] = 1;
                        else if ($b == 'd') $user_bet[$k] = 0;
                        else 
                        {
                            $params_vailure = false;
                            break;
                        }
                    }
                    break;
            }

            if ($params_vailure)
            {
                // Autoloader::add_class('game\play\Deal', APPPATH.'game/play/deal.php');
                $deal = new Deal();
                $deal_result = $deal->bet_deal_bacth($amount, $user_id[1], $user_bet, $rid, $pid, $type);
                if($deal_result['code'])
                {
                    \Fuel\Core\Log::error('inoutdeal, line 46 error : '.$deal_result['message']);
                    return $this->response(array('code' => '3', 'message' => Lang::get('error.ER5')));
                }
            }
            else
            {
                return $this->response(array('code' => '5', 'message' => Lang::get('error.ER1')));
            }
        }
        else //Prohibited bet, not within timing
            return $this->response(array('code' => '4', 'message' => Lang::get('error.ER5')));
            
        $set_userdata = array(
            'type' => $type,
            'amount' => $amount,
            'bet' => $user_bet,
            'time' => strtotime('now'),
        );
        $this->redis->set($pid.":".$user_id[1], json_encode($set_userdata));
        $this->redis->expire($pid.":".$user_id[1], $this->deadline_time);
        $this->redis->del($pid.":".$user_id[1]."betting");
        
        return $this->response(array(
            'code' => "0",
            'message' => 'success',
            'data' => $deal_result['data'],
        ));
    }
}