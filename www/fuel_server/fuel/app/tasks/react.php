<?php
namespace Fuel\Tasks;

use Fuel\Core\Debug;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use game\ws\WebSocket;
use game\ws\WampServerSocket;
use game\ws\WsPublish;
class React
{
    public static function run()
    {
//        echo "Run the server build...";
//        $server = IoServer::factory(new WsServer(new WebSocket()), 8080);
//        echo "websocket do run...";
//        $server->run();
//        echo "websocket sever up...";
        React::runWampServer();
    }

    public  static function runWampServer()
    {
        WampServerSocket::run();
    }

    public  static function pusher()
    {
        WsPublish::getInstance();
    }
}