<?php

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use MarktApp\ChatConnection;

require "app/chat_websocket_config.php";
require "vendor/autoload.php";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatConnection
        )
    ),
    8080
);

$server->run();