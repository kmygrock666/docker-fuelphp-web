<?php


namespace game\play;

use Fuel\Core\Config;
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

    public static function bet($gameTpye, array $exclude = array(), array $eligible = array())
    {

    }

    public static function period($gameTpye, array $exclude = array(), array $eligible = array())
    {
        WsPublish::send($gameTpye, Config::get('myconfig.topic.period'), GamePusher::getGameInstance($gameTpye)->getPeriod());
    }

    public static function history($gameTpye, array $exclude = array(), array $eligible = array())
    {
        WsPublish::send($gameTpye, Config::get('myconfig.topic.history'), GamePusher::getGameInstance($gameTpye)->getHistory());
    }

    public static function winner($gameTpye, array $exclude = array(), array $eligible = array())
    {

    }

    public static function getData($gamyType, $command)
    {
        switch ($command) {
            case 'history':
                return GamePusher::getGameInstance($gamyType)->getHistory();
            default:
                break;
        }
        return null;
    }

}