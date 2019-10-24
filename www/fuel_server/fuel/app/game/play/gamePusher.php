<?php


namespace game\play;

use Fuel\Core\Config;
use Fuel\Core\Debug;
use Fuel\Core\Log;
use game\ws\WsPublish;

class GamePusher
{
    /** 取得遊戲類型class
     * @param $gameTpye 遊戲代號
     * @return UltimatPassword|mixed
     */
    public static function getGameInstance($gameTpye)
    {
        $instance = json_decode('{}');
        switch ($gameTpye) {
            case 'up':
                $instance = UltimatPassword::getInstance();
                break;
            default:
                break;
        }

        return $instance;
    }
    /** 下注返回
     * @param $gameTpye 遊戲代號
     * @param $request 下注資料
     * @param $userId 用戶id
     * @return array
     */
    public static function bet($gameTpye, $request, $userId)
    {
        $gameClass = GamePusher::getGameInstance($gameTpye);
        return $gameClass->betGame($request, $userId);
    }
    /** 期數
     * @param $gameTpye 遊戲代號
     * @param array $exclude 排除
     * @param array $eligible 包含
     */
    public static function period($gameTpye, array $exclude = array(), array $eligible = array())
    {
        $key = Config::get('myconfig.topic.period');
        WsPublish::send($gameTpye, $key, GamePusher::getData($gameTpye, $key));
    }
    /** 歷史紀錄
     * @param $gameTpye 遊戲代號
     * @param array $exclude 排除
     * @param array $eligible 包含
     */
    public static function history($gameTpye, array $exclude = array(), array $eligible = array())
    {
        $key = Config::get('myconfig.topic.history');
        WsPublish::send($gameTpye, $key, GamePusher::getData($gameTpye, $key));
    }
    /** 中獎
     * @param $gameTpye 遊戲代號
     * @param array $exclude 排除
     * @param array $eligible 包含
     */
    public static function winner($gameTpye, array $exclude = array(), array $eligible = array())
    {
        $getWin = GamePusher::getGameInstance($gameTpye)->getWinnerUser();
        foreach ($getWin as $type) {
            foreach ($type as $b) {
                WsPublish::send($gameTpye, Config::get('myconfig.topic.winner'), $b['data'], array(), $b['id']);
            }
        }
    }
    /** 取得data
     * @param $gamyType 遊戲代號
     * @param $command 訂閱主題
     * @return array|null
     */
    public static function getData($gamyType, $topic)
    {
        switch ($topic) {
            case Config::get('myconfig.topic.history'):
                return GamePusher::getGameInstance($gamyType)->getHistory();
            case Config::get('myconfig.topic.period'):
                return GamePusher::getGameInstance($gamyType)->getPeriod();
            default:
                break;
        }
        return null;
    }

}