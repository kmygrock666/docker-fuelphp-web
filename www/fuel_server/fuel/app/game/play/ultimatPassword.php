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

    /**
     * @param $pid 期數
     * @param $r 回合id
     * @param $ans 終極密碼
     * @param $max 最大
     * @param $min 最小
     * @param $number 當回合開獎號碼
     */
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
    /** 結算
     * @param $name 遊戲類型
     * @param $isSettle 是否結算
     * @return null
     */
    public function settle($gameType, $isSettle)
    {
        if (array_key_exists($gameType, $this->games))
        {
            return $this->games[$gameType]->getResult($isSettle);
        }
        return null;
    }
    /** 取賠率
     * @param $name 遊戲類型
     * @return null
     */
    public function getRate($gameType)
    {
        if (array_key_exists($gameType, $this->games))
        {
            return $this->games[$gameType]->getRate();
        }
        return null;
    }
    /** 取期數
     * @return array
     */
    public function getPeriod()
    {
        return $this->game_operate->processPeriodTimer();
    }
    /** 取歷史紀錄
     * @return array
     */
    public function getHistory()
    {
        return $this->game_operate->processHistoryPeriod();
    }
    /** 取得中獎會員
     * @return array
     */
    public function getWinnerUser()
    {
        $win = array();
        $win['SDP'] = $this->game_operate->processWinnerUsers($this->games['SDP']->getWinnerUser());
        $win['NP'] = $this->game_operate->processWinnerUsers($this->games['NP']->getWinnerUser());
        return $win;
    }
    /** 下注
     * @param $betData 下注資料
     * @param $userId 用戶id
     * @return array
     */
    public  function betGame($betData, $userId)
    {
        return $this->game_operate->processBetGame($betData, $userId);
    }


}