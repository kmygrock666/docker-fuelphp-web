<?php

namespace game\play;
use game\GamePlay;

use Model_Period;
use Model_Round;
use Model_Bet;

class SDPlay extends GamePlay{

    protected $gt = 2;
    protected $max = 40;
    protected $min = 1;
    protected $even = 0;
    protected $odd = 0;

    function __construct($pid, $r, $ans, $max, $min)
    {
        
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->max = $max;
        $this->min = $min;
    }

    public function getResult()
    {
        return $this->getBets();
    }

    private function getSingleDouble()
    {
        for($i = $this->min ; $i <= $this->max; $i++)
        {
            if($i % 2 == 0) $this->even ++;
            else $this->odd ++;
        }
        $this->all_number = $this->even + $this->odd;
    }

    private function getBets()
    {
        $this->getSingleDouble();
        $bets = Model_Bet::find_bet($this->pid, $this->gt, $this->round);
        $flag = false;
        if($bets->count() == 0)
        {
            echo "not found <br>";
        }
        else
        {
            $isSingleDouble = $this->answer % 2;
            if($isSingleDouble == 0) $this->optional_number = $this->even; //double
            else $this->optional_number = $this->odd; //single


            foreach($bets as $bet)
            {
                
                if ($bet->bet_number == $isSingleDouble)
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