<?php

namespace game\play;
use game\GamePlay;

use Model_Period;
use Model_Round;
use Model_Bet;
use game\play\Deal;

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
        $this->getSingleDouble();
    }

    function getRate()
    {
        return $this->getPlayRate();
    }

    public function getResult()
    {
        return $this->getBets();
    }

    public function setSelected($cmd)
    {
        if($cmd == 0) $this->optional_number = $this->even; //double
        else $this->optional_number = $this->odd; //single
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
        $sd = $this->setSingle_or_double();
        $bets = Model_Bet::find_bet($this->pid, $this->gt, $this->round, 0);
        $flag = false;
        if($bets->count() == 0)
        {
            echo "not found from SDPlay<br>";
        }
        else
        {
            $deal = new Deal();
            foreach($bets as $bet)
            {
                if ($bet->bet_number == $sd)
                {
                    $payout = $bet->amount * $this->getPlayRate() ;
                    $r = $deal->send_bonus($bet, $payout);
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

    private function setSingle_or_double()
    {
        $isSingleDouble = $this->answer % 2;
        if($isSingleDouble == 0) $this->optional_number = $this->even; //double
        else $this->optional_number = $this->odd; //single
        return $isSingleDouble;
    }

}