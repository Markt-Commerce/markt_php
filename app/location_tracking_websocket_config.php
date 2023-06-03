<?php

namespace MarktApp;

require "vendor/autoload.php";
require "../orders.php";

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Markt\orders\order;

class LocationTrackConnection implements MessageComponentInterface{

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn){
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from,$message){
        $decoded_message = json_decode($message);
        if(!empty($decoded_message["register_id"])){
            if($this->clients->contains($from)){
                $this->clients[$from] = $decoded_message["register_id"];
            }
        }
        elseif(!empty($decoded_message["order_id"]) && !empty($decoded_message["latitude"]) && !empty($decoded_message["longtitude"]) && !empty($decoded_message["delivery_id"])){
            $order = new order($decoded_message["order_id"]);
            if($order->is_delivery_assigned() && $order->has_location_tracking()){
                if($order->delivery_id == $decoded_message["delivery_id"]){
                    $order->set_delivery_location($decoded_message["longtitude"],$decoded_message["latitude"]);
                    foreach($this->clients as $client){
                        if($this->clients[$client] == $order->buyer_id){
                            $client->send($message);
                        }
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn){
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn,\Exception $e){
        echo "An error has occurred: {$e->getMessage()}\n"; 

        $conn->close();
    }
}