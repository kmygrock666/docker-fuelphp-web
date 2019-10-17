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

    private function __clone() { }

    public static function getInstance()
    {
        if ( ! self::$instance)
            self::$instance = new WsPublish();
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
     * @throws \ZMQSocketException
     */
    public static function send($gameType, $category, $data)
    {
        $entryData = array(
            'gameType' => $gameType,
            'category' => $category,
            'title'    => $category,
            'entryData'  => $data,
            'when'     => time()
        );
        if(WsPublish::getInstance()->getbrodcast()){
            WsPublish::getInstance()->getbrodcast()->send(json_encode($entryData));
            echo "success";
        }
    }
}