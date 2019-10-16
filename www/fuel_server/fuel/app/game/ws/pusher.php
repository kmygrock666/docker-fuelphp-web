<?php
namespace game\ws;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface;
use Fuel\Core\Log;

class Pusher implements WampServerInterface
{
    protected $subscribedTopics = array();
    public function onSubscribe(ConnectionInterface $conn, $topic) {
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

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);
    }

    public function onOpen(ConnectionInterface $conn) {
        $conn->send(json_encode('onOpen'));
    }

    public function onClose(ConnectionInterface $conn) {
        Log::error("onClose");
        $conn->send('onClose');
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        Log::error("onCall");
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        Log::error(sprintf("onPublish, topic: %s, event: %s, exclude: %s, eligible: %s", $topic, json_encode($event), json_encode($exclude), json_encode($eligible)));
        $conn->send(json_encode('onPublish'));
//        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}