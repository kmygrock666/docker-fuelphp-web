<?php

namespace game\play;
use game\GamePlay;

use game\play\Deal;
use Model_Bet;

class NumberPlay extends GamePlay{

    protected function init() {  }

    function __construct($pid, $r, $ans, $max, $min)
    {
        $this->gt = 1;
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->max = $max;
        $this->min = $min;
        $this->optional_number = 1;
        $this->all_number = $max - $min + 1;
    }

    function getRate()
    {
        return $this->getPlayRate();
    }

    public function getResult()
    {
        return $this->getBets();
    }

    private function getBets()
    {
        $bets = Model_Bet::find_bet($this->pid, $this->gt, $this->round, 0);
        $flag = false;
        if($bets->count() == 0)
        {
            echo "not found from NumberPlay <br>";
        }
        else
        {
            // Autoloader::add_class('game\play\Deal', APPPATH.'game/play/deal.php');
            $deal = new Deal();
            foreach($bets as $bet)
            {
                if($bet->bet_number == $this->answer)
                {
                    $payout = $bet->amount * $this->getPlayRate() ;
                    $deal->send_bonus($bet, $payout);
                    $flag = true;
                }
                else
                {
                    $bet->isWin = 2;
                    $bet->save();
                }
            }
        }

        return $flag;

    }

}