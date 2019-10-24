<?php
namespace game;
use game\BetImpl;

abstract class GamePlay implements BetImpl {

    protected $gt; //遊戲類型
    protected $all_number = 1; //全部
    protected $optional_number = 1; //可下數量
    protected $max = 40;
    protected $min = 1;
    protected $answer = 1; //終極密碼
    protected $pid = 0; //期數
    protected $round = 0; //回合數
    protected $number = 0; // 每回合號碼
    protected $winner_user = array(); //中獎user

    protected function getPlayRate()
    {
        $c = pow(10, 2);
        return floor(((1 / ($this->optional_number / $this->all_number)) * 0.92) * $c) / $c;
    }

    public function setGameParams($pid, $r, $ans, $max, $min, $number)
    {
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->max = $max;
        $this->min = $min;
        $this->all_number = $max - $min + 1;
        $this->number = $number;
        $this->init();
    }

    abstract function getRate();
    abstract protected function init();

     

}