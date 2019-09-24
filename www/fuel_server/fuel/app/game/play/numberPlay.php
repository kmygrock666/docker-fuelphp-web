<?php

namespace game\play;
use game\GamePlay;

use Model_Period;
use Model_Round;
use Model_Bet;

class NumberPlay extends GamePlay{

    protected $gt = 1;
    protected $bet_level = 5;

    function __construct($pid, $r, $ans, $total)
    {
        
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->optional_number = 1;
        $this->all_number = $total;
    }

    public function getResult()
    {
        return $this->getBets();
    }

    private function getBets()
    {
        $bets = Model_Bet::find_bet($this->pid, $this->gt, $this->round);
        $flag = false;
        if($bets->count() == 0)
        {
            echo "not found <br>";
        }
        else
        {
            foreach($bets as $bet)
            {
                if($bet->bet_number == $this->answer)
                {
                    $bet->isWin = true;
                    $bet->payout = $bet->amount * (($this->getPlayRate() / 100) + 1) ;
                    $bet->save();
                    $flag = true;
                }
            }
        }

        return $flag;

    }

}