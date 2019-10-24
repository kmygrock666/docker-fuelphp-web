<?php

namespace game\ws;

use Fuel\Core\Debug;
use Fuel\Core\Log;
use Oil\Exception;

class WsPublish
{
    private static $instance;
    private static $brodcast;

    private function __construct()
    {
        self::$brodcast = WampServerSocket::connect_borcats();
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if ( ! self::$instance) {
            self::$instance = new WsPublish();
        }
        return self::$instance;
    }

    public function getbrodcast()
    {
        return self::$brodcast;
    }

    /**
     * @param $gameType 遊戲類別
     * @param $category 推播類型
     * @param $data 預傳資料
     * @param $exclude 排除 ids
     * @param $eligible 包含 ids
     */
    public static function send($gameType, $category, $data, array $exclude = array(), array $eligible = array())
    {
        $entryData = array(
            'gameType' => $gameType,
            'category' => $category,
            'title'    => $category,
            'data'     => $data,
            'exclude'  => $exclude,
            'eligible' => $eligible,
            'when'     => time()
        );
        if (WsPublish::getInstance()->getbrodcast()) {
            WsPublish::getInstance()->getbrodcast()->send(json_encode($entryData));
            return;
        }
    }
}