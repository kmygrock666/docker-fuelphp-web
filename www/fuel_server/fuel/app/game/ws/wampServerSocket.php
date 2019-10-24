<?php

namespace game\ws;

require __DIR__ . '/../../../vendor/autoload.php';

use Fuel\Core\Log;
use Oil\Exception;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use ZMQ;
use React\Socket\Server;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use ZMQContext;
use game\ws\WsPublish;

class WampServerSocket
{
    public static function run()
    {
        try {
            $loop = Factory::create();
            $pusher = new Pusher;

            // Listen for the web server to make a ZeroMQ push after an ajax request
            $context = new Context($loop);
            $pull = $context->getSocket(ZMQ::SOCKET_PULL);
            $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
            $pull->on('message', array($pusher, 'onBlogEntry'));

            // Set up our WebSocket server for clients wanting real-time updates
            $webSock = new Server('0.0.0.0:8080', $loop); // Binding to 0.0.0.0 means remotes can connect
            $webServer = new IoServer(new HttpServer(new WsServer(new WampServer($pusher))), $webSock);
            $loop->run();
        } catch (Exception $e) {
            Log::error("WampServerSocket->start exception in line 37 => " . $e->getMessage());
            echo "Error->" . $e->getMessage();
        }
    }
    /*
     * 連線websocket
     */
    public static function connect_borcats()
    {
        $context = new ZMQContext();
        $brodcasts = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
        $brodcasts->connect("tcp://localhost:5555");
        return $brodcasts;
    }


}