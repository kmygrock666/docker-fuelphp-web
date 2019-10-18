<?php
namespace game\ws;

use game\play\UltimatPassword;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface;
use Fuel\Core\Log;
use Fuel\Core\Config;
use game\play\GamePusher;


class Pusher implements WampServerInterface
{
    protected $subscribedTopics = array();
    protected $connected_user = array();

    public function __construct()
    {
        $this->subscribedTopics = array(
            Config::get('myconfig.topic.bet') => array(), //下注返回
            Config::get('myconfig.topic.period') => array(), //期數
            Config::get('myconfig.topic.history') => array(), //歷史紀錄
            Config::get('myconfig.topic.winner') => array(), //中獎通知
        );
    }

    public function onSubscribe(ConnectionInterface $conn, $topic) {
//        Log::error("onSubscribe->".$topic->getId()." topic=> ". count($this->subscribedTopics));
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
            return;
        }
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }

    public function onBlogEntry($entry) {
        $entryData = json_decode($entry, true);
//        Log::error("onBlogEntry->$entryData: ". print_r($entryData, true));
        if ( ! array_key_exists($entryData['category'], $this->subscribedTopics))  return;
        // If the lookup topic object isn't set there is no one to publish to
        $topic = $this->subscribedTopics[$entryData['category']];
        if ( ! count($topic)) return;

        $exclude = $entryData['exclude'];
        $eligible = $entryData['eligible'];
        $exclude_arr = array();
        $eligible_arr = array();
        foreach ($this->connected_user as $sessionId => $userId) {
            if (in_array($userId, $exclude)) array_push($exclude_arr, $sessionId);
            if (in_array($userId, $eligible)) array_push($eligible_arr, $sessionId);
        }

        unset($entryData['exclude']);
        unset($entryData['eligible']);

//        Log::error("onBlogEntry->exclude: ".count($exclude)." /eligible: ".count($eligible));
        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData, $exclude_arr, $eligible_arr);
    }

    public function onOpen(ConnectionInterface $conn) {
        $conn->send(json_encode('conection success!!'));
    }

    public function onClose(ConnectionInterface $conn) {
        Log::error("onClose");
        unset($this->connected_user[$conn->WAMP->sessionId]);
//        $conn->send('onClose');
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        Log::error("onCall");
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $getData = json_decode($event, true);
        if ($topic == "user") {
            if (array_key_exists('gt', $getData) and array_key_exists('userId', $getData)) {
                if(WsController::checkOnline($this->connected_user, $getData['userId'])) {
                    Log::error("close line 86");
                    $conn->callError('1001', $topic, 'already login')->close();
                } else {
                    $this->connected_user[$conn->WAMP->sessionId] = array('gt' => $getData['gt'], 'uid' => $getData['userId']);
                    $conn->send(json_encode('[success]'));
                }
            } else {
                $conn->send(json_encode('[0]'));
            }

        } elseif (array_key_exists($conn->WAMP->sessionId, $this->connected_user)) {
            if (WsController::process($conn, $topic, $getData, $this->connected_user[$conn->WAMP->sessionId]['gt'])) {
            } else {
                $conn->send(json_encode('[1]'));
            }
        } else {
            $conn->send(json_encode('[2]'));
        }



//        Log::error(sprintf("onPublish, topic: %s, event: %s, resourceId: %s, eligible: %s", $topic, json_encode($event), $conn->resourceId, json_encode($eligible)));
//        $conn->send(json_encode('onPublish'));
//        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}