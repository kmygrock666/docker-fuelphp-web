<?php


namespace game\play;
use Fuel\Core\Config;
use Fuel\Core\Redis_Db;
use Model_Period;

class Games
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

    public function getPeriodTimer()
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

    public function get_historyPeriod()
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
}