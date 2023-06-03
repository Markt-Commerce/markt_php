<?php

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use MarktApp\LocationTrackConnection;

require "app/location_tracking_websocket_config.php";
require "vendor/autoload.php";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new LocationTrackConnection
        )
    ),
    3000
);

$server->run();