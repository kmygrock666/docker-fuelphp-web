<?php


namespace game\play;

use Fuel\Core\Config;
use Fuel\Core\Debug;
use Fuel\Core\Log;
use game\ws\WsPublish;

class GamePusher
{
    //取得遊戲類型class
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
    //下注返回
    public static function bet($gameTpye, $request, $userId)
    {
        $gameClass = GamePusher::getGameInstance($gameTpye);
        return $gameClass->betGame($request, $userId);
    }
    //期數
    public static function period($gameTpye, array $exclude = array(), array $eligible = array())
    {
        $key = Config::get('myconfig.topic.period');
        WsPublish::send($gameTpye, $key, GamePusher::getData($gameTpye, $key));
    }
    //歷史紀錄
    public static function history($gameTpye, array $exclude = array(), array $eligible = array())
    {
        $key = Config::get('myconfig.topic.history');
        WsPublish::send($gameTpye, $key, GamePusher::getData($gameTpye, $key));
    }
    //中獎
    public static function winner($gameTpye, array $exclude = array(), array $eligible = array())
    {
        $getWin = GamePusher::getGameInstance($gameTpye)->getWinnerUser();
        foreach ($getWin as $type) {
            foreach ($type as $b) {
                WsPublish::send($gameTpye, Config::get('myconfig.topic.winner'), $b['data'], array(), $b['id']);
            }
        }
    }
    //取得data
    public static function getData($gamyType, $command)
    {
        switch ($command) {
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