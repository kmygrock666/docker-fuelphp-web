<?php
namespace game;
use game\BetImpl;

abstract class GamePlay implements BetImpl {

    protected $gt;
    protected $all_number = 1;
    protected $optional_number = 1;
    protected $max = 40;
    protected $min = 1;
    protected $answer = 1;
    protected $pid = 0;
    protected $round = 0;

    protected function getPlayRate()
    {
        $c = pow(10, 2);
        return floor(((1 / ($this->optional_number / $this->all_number)) * 0.92) * $c) / $c;
    }

    public function setGameParams($pid, $r, $ans, $max, $min)
    {
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->max = $max;
        $this->min = $min;
        $this->init();
    }

    abstract function getRate();
    abstract protected function init();

     

}