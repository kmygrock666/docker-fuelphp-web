<?php

namespace game\play;

use Fuel\Core\Debug;
use game\GamePlay;

use Model_Period;
use Model_Round;
use Model_Bet;
use game\play\Deal;

class SDPlay extends GamePlay{

    protected $even = 0;
    protected $odd = 0;

    protected function init()
    {
        $this->even = 0;
        $this->odd = 0;
        $this->getSingleDouble();
    }

    function __construct($pid, $r, $ans, $max, $min, $number)
    {
        $this->gt = 2;
        $this->pid = $pid;
        $this->round = $r;
        $this->answer = $ans;
        $this->max = $max;
        $this->min = $min;
        $this->number = $number;
        $this->init();
    }

    function getRate()
    {
        $this->setSelected(0);
        $even = $this->getPlayRate();
        $this->setSelected(1);
        $odd = $this->getPlayRate();
        return array($even, $odd);
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

    private function setSelected($cmd)
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

    private function getBets($isSettle)
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
                    if($r['code'] == 1) return $r['message'];
                    $this->winner_user[$bet->id] = $bet;
                    $flag = true;
                }
                else
                {
                    $bet->status = 2;
                    $bet->save();
                }
                
            }
        }
        return $flag;
    }

    private function setSingle_or_double()
    {
        $isSingleDouble = $this->number % 2;
        if($isSingleDouble == 0) $this->optional_number = $this->even; //double
        else $this->optional_number = $this->odd; //single
        return $isSingleDouble;
    }

}