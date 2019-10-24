<?php


namespace game\play;
use Fuel\Core\Config;
use Fuel\Core\Log;
use Fuel\Core\Redis_Db;
use Model_Period;
use Fuel\Core\Lang;

class GamesProcess
{
    protected $redis;
    protected $pid;
    protected $stop_time;
    protected $wait_time;

    public function __construct()
    {
        $this->redis = Redis_Db::instance();
        $this->pid = Config::get('myconfig.period.pid');
        $this->stop_time = Config::get('myconfig.period.stop_time');
        $this->wait_time = Config::get('myconfig.period.wait_time');
    }

    private function response($code, $message, $data = array())
    {
        return array('code' => $code, 'message' => $message, 'data' => $data);
    }

    public function processPeriodTimer()
    {
        $periodList = $this->redis->get($this->pid);

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

        if ($period->close) {
            $data['status'] = "close";
            $data['pwd'] = $period->pwd;
            $data['time'] = $this->wait_time - ($period->time % $this->stop_time);
        } else {
            if ($period->time <= $this->stop_time) {
                $data['status'] = "bet";
                $data['time'] = $this->stop_time - $period->time;
            } elseif ($period->time <= ($this->stop_time + $this->wait_time)) {
                $data['status'] = "stop";
                $data['time'] = $this->wait_time - ($period->time % $this->stop_time);
            }
        }

        return $data;
    }

    public function processHistoryPeriod()
    {
        $data = array();
        $period = Model_Period::find_period_lastest(true);
        if (isset($period)) {
            $data['pid'] = $period->pid;
            $data['open'] = $period->open_win;
            $data['round'] = array();

            foreach ($period->round as $r) {
                array_push($data['round'], $r->open_win);
            }
        }

        return $data;
    }

    public function processWinnerUsers($userdata)
    {
        $data = array();
        foreach($userdata as $key => $bet) {
                $tmp = array();
                $betdata = array();
                $betdata['type'] = $bet->type;
                $betdata['num'] = $bet->bet_number;
                $betdata['payout'] = $bet->payout;
                $tmp['data'] = $betdata;
                $tmp['id'] = array($bet->user_id);
                array_push($data, $tmp);
        }
        return $data;
    }

    //批量下注
    public function processBetGame($betData, $userId)
    {
        $pid = $this->pid;
        $periodList = $this->redis->get($pid);

        if ($periodList == null) {
            return $this->response('5', Lang::get('error.ER6'));
        }

        $singleDoubleCode = array(
          's' => 1,
          'd' => 0,
        );
        $period = json_decode($periodList);

        $pid = $period->pid;
        $rid = $period->round;
        $user_bet = $betData['b'];
        $type = $betData['t'];
        $amount = $betData['m'];
        $user_id = $userId;

        if ($user_bet == null || $type == null || $amount == null) {
            return $this->response('1', Lang::get('error.ER1'));
        }

        if ($period->close == false && $period->time <= $this->stop_time) {

            $params_vailure = true;

            switch ($type) {
                case 1: //number
                    foreach ($user_bet as $k => $b) {
                        if (is_numeric($b)) {
                            if ($b < $period->min or $b > $period->max) {
                                $params_vailure = false;
                                break;
                            }
                        }
                    }

                    break;
                case 2: //single Double
                    foreach ($user_bet as $k => $b) {
                        if (array_key_exists($b, $singleDoubleCode)) {
                            $user_bet[$k] = $singleDoubleCode[$b];
                        } else {
                            $params_vailure = false;
                            break;
                        }
                    }
                    break;
            }

            if ($params_vailure) {
                //限制n秒內不能連續下注
                $userdata_redis = $this->redis->get($pid . ":" . $user_id);
                if ($userdata_redis != null) {
                    $userdata = json_decode($userdata_redis, true);

                    if (strtotime('now') - $userdata['time'] < $this->wait_time) {
                        $error = 1;
                        if ($userdata['type'] == $type) {
                            $error++;
                        }

                        $old_bet = $userdata['bet'];
                        if (count($user_bet) == count($old_bet)) {
                            $error++;
                            foreach ($old_bet as $k => $b) {
                                if ($b !== $user_bet[$k]) {
                                    $error --;
                                    break;
                                }
                            }
                        }

                        if ($userdata['amount'] == $amount) {
                            $error++;
                        }

                        if ($error >= count($userdata)) {
                            return $this->response('6', Lang::get('error.ER7'));
                        }
                    }
                }

                //下注未完成，禁止再下注
                $userdata_betting_redis = $this->redis->get($pid . ":" . $user_id . "betting");
                if ($userdata_betting_redis != null) {
                    if ($userdata_betting_redis) {
                        return $this->response('7', Lang::get('error.ER8'));
                    }
                } else {
                    $this->redis->set($pid . ":" . $user_id . "betting", true);
                }

                //寫入db
                $deal = new Deal();
                $deal_result = $deal->bet_deal_bacth($amount, $user_id, $user_bet, $rid, $pid, $type);
                if ($deal_result['code']) {
                    \Fuel\Core\Log::error('inoutdeal, line 46 error : ' . $deal_result['message']);
                    return $this->response('3', Lang::get('error.ER5'));
                }
            } else {
                return $this->response('5', Lang::get('error.ER1'));
            }
        } else //Prohibited bet, not within timing
        {
            return $this->response('4', Lang::get('error.ER5'));
        }

        $set_userdata = array(
            'type'   => $type,
            'amount' => $amount,
            'bet'    => $user_bet,
            'time'   => strtotime('now'),
        );
        $this->redis->set($pid . ":" . $user_id, json_encode($set_userdata));
        $this->redis->expire($pid . ":" . $user_id, $this->wait_time);
        $this->redis->del($pid . ":" . $user_id . "betting");

        return $this->response("0", 'success', $deal_result['data']);
    }
}