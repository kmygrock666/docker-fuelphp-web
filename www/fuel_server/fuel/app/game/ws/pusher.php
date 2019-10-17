<?php
namespace game\ws;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface;
use Fuel\Core\Log;

class Pusher implements WampServerInterface
{
    protected $subscribedTopics = array(
        'bet' => array(), //下注返回
        'period' => array(), //期數
        'winner' => array(), //中獎通知
    );

    protected $connected_user = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
//        Log::error("onSubscribe->".$topic->getId()." topic=> ". print_r($topic, true));
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
            return;
        }
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }

    public function onBlogEntry($entry) {
        $entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$entryData['category']];
//        Log::error("onBlogEntry->". print_r($topic, true));
        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);
    }

    public function onOpen(ConnectionInterface $conn) {
        $conn->send(json_encode('conection success!!'));
    }

    public function onClose(ConnectionInterface $conn) {
        Log::error("onClose");
        unset($this->connected_user[$conn->resourceId]);
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
        if (array_key_exists('tp', $getData)){
            switch ($getData['tp']) {
                case '0':
                    if(array_key_exists('user_id', $getData))
                        $this->connected_user[$conn->resourceId] = $getData['user_id'];
                    break;
                default:
                    break;
            }
        }

        Log::error(sprintf("onPublish, topic: %s, event: %s, resourceId: %s, eligible: %s", $topic, json_encode($event), $conn->resourceId, json_encode($eligible)));
//        $conn->send(json_encode('onPublish'));
//        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}