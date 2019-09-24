<?php
namespace game;
use game\BetImpl;

abstract class GamePlay implements BetImpl {

    protected $all_number = 0;
    protected $optional_number = 0;
    protected $answer;
    protected $pid;
    protected $round;

    public function getPlayRate()
    {
        $c = pow(10, 2);
        return floor(((1 / ($this->optional_number / $this->all_number)) * 0.92) * $c) / $c;
    }
}