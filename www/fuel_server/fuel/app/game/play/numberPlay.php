<?php

namespace game\play;

use Fuel\Core\Debug;
use game\GamePlay;

use game\play\Deal;
use Model_Bet;

class NumberPlay extends GamePlay{

    protected function init() {  }

    function __construct($pid, $r, $ans, $max, $min, $number)
    {
        $this->gt = 1;
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->max = $max;
        $this->min = $min;
        $this->optional_number = 1;
        $this->all_number = $max - $min + 1;
        $this->number = $number;
    }

    function getRate()
    {
        return $this->getPlayRate();
    }

    function getWinnerUser()
    {
        return $this->winner_user;
    }

    public function getResult($isSettle)
    {
        $this->winner_user = array();
        return $this->getBets($isSettle);
    }

    private function getBets($isSettle)
    {
        $bets = Model_Bet::find_bet($this->pid, $this->gt, $this->round, 0);
        $flag = false;
        if($bets->count() == 0)
        {
            echo "not found from NumberPlay <br>";
        }
        else
        {
            $deal = new Deal();
            
            foreach($bets as $bet)
            {
                //進行輸贏判斷
                if($isSettle)
                {
                    if ($bet->bet_number == $this->answer) {
                        $payout = $bet->amount * $this->getPlayRate();
                        $r = $deal->send_bonus($bet, $payout);
                        if ($r['code'] == 1) return $r['message'];
                        $this->winner_user[$bet->user_id] = $bet;
                        $flag = true;
                    } else {
                        $bet->status = 2;
                        $bet->save();
                    }
                }
                else
                {
                    $r = $deal->refund($bet);
                    if ($r['code'] == 1) return $r['message'];
                }
            }
        }

        return $flag;

    }

}