<?php


namespace game\ws;
use Auth\Auth;
use Fuel\Core\Log;
use game\play\GamePusher;
use Fuel\Core\Config;

class WsController
{
    /** 檢查是否已在遊戲內
     * @param $onlines 線上用戶
     * @param $newClient 新連線用戶
     * @return bool
     */
    public static function checkOnline($onlines, $newClient)
    {
        foreach ($onlines as $sid => $userdata) {
            if ($userdata['uid'] == $newClient) {
                return true;
            }
        }

        return false;
    }

    /** 請求處理
     * @param ConnectionInterface $conn
     * @param $topic 請求項目
     * @param $event 傳遞資料
     * @param $gt 那一款遊戲
     */
    public static function process($conn, $topic, $event, $gd)
    {
        $boolean = false;
        Log::error(json_encode(Auth::get_user_id()));
        switch ($topic) {
            case Config::get('myconfig.topic.history'):
                $conn->event($topic, array('data' => GamePusher::getData($gd['gt'], $topic)));
                $boolean = true;
                break;
            case Config::get('myconfig.topic.bet'):
                $conn->event($topic, GamePusher::bet($gd['gt'], $event, $gd['uid']));
                $boolean = true;
                break;
            default:
                break;
        }
        return $boolean;
    }

}