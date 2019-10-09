<?php
namespace game\ws;
/**
 * websocket.php
 * Send any incoming messages to all connected clients (except sender)
 */
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
require __DIR__ . '/../../../vendor/autoload.php';

class WebSocket implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        echo "__construct";
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "onOpen";
        $this->clients->attach($conn);
        echo __CLASS__."::".__FUNCTION__."\n";
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $message) {
        echo __CLASS__."::".__FUNCTION__."({$message})\n";
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send("message:" . $message);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        echo "onClose";
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "onError";
        $conn->close();
        echo __CLASS__."::".__FUNCTION__."\n";
        echo "An error has occurred: {$e->getMessage()}\n";
    }
}

// Run the server application through the WebSocket protocol on port 8080
//echo "Run the server...";
//$server = IoServer::factory(new WsServer(new WebSocket()), 8080);
//$server->run();
//echo "websocket sever up...";