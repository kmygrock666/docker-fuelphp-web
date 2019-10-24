<?php
namespace game\play;
use Fuel\Core\Debug;
use Fuel\Core\Log;
use game\play\SDPlay;
use game\play\NumberPlay;
use game\play\GamesProcess;

class UltimatPassword
{
    private  static $instance;
    protected $games = array(); //遊戲實例
    protected $game_operate;


    private function __construct()
    {
        $this->game_operate = new GamesProcess();
    }

    private function __clone() { }

    public static function getInstance()
    {
        if ( ! self::$instance)
            self::$instance = new UltimatPassword();
        return self::$instance;
    }

    public function create_play($pid, $r, $ans, $max, $min, $number)
    {
        if(count($this->games) == 0)
        {
            $this->games['SDP'] = new SDPlay($pid, $r, $ans, $max, $min, $number);
            $this->games['NP'] = new NumberPlay($pid, $r, $ans, $max, $min, $number);
        }
        else
        {
            $this->games['SDP']->setGameParams($pid, $r, $ans, $max, $min, $number);
            $this->games['NP']->setGameParams($pid, $r, $ans, $max, $min, $number);
        }
        
    }
    //結算
    public function settle($name, $isSettle)
    {
        if (array_key_exists($name, $this->games))
        {
            return $this->games[$name]->getResult($isSettle);
        }
        return null;
    }
    //取賠率
    public function getRate($name)
    {
        if (array_key_exists($name, $this->games))
        {
            return $this->games[$name]->getRate();
        }
        return null;
    }
    //取期數
    public function getPeriod()
    {
        return $this->game_operate->processPeriodTimer();
    }
    //取歷史紀錄
    public function getHistory()
    {
        return $this->game_operate->processHistoryPeriod();
    }
    //取得中獎會員
    public function getWinnerUser()
    {
        $win = array();
        $win['SDP'] = $this->game_operate->processWinnerUsers($this->games['SDP']->getWinnerUser());
        $win['NP'] = $this->game_operate->processWinnerUsers($this->games['NP']->getWinnerUser());
        return $win;
    }
    //下注
    public  function betGame($betData, $userId)
    {
        return $this->game_operate->processBetGame($betData, $userId);
    }


}