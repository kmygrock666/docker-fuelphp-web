<?php
namespace Fuel\Tasks;

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use game\ws\WebSocket;

class React
{
    public static function run()
    {
        echo "Run the server build...";
        $server = IoServer::factory(new WsServer(new WebSocket()), 8080);
        echo "websocket do run...";
        $server->run();
        echo "websocket sever up...";
    }
}