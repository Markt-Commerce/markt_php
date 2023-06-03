<?php

namespace MarktApp;

require "vendor/autoload.php";
require "chat.php";

use Markt\Chat;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatConnection implements MessageComponentInterface{

    protected $clients;

    protected $messageclients;

    public function __construct() {
        $this->messageclients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn){
        $this->messageclients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from,$message){
        $new_message = json_decode($message,true);
        if(!empty($new_message["register_id"])){
            if($this->messageclients->contains($from)){
                $this->messageclients[$from] = $new_message["register_id"];
            }
        }
        elseif(!empty($new_message["sent_to"]) && !empty($new_message["sent_from"]) && !empty($new_message["message"])){
            if($this->messageclients->contains($from)){
                $chat = new Chat($new_message);
                $chat->send_message($new_message["sent_to"]);
                foreach($this->messageclients as $message_client){
                    if($this->messageclients[$message_client] == $new_message["sent_to"] || $this->messageclients[$message_client] == $new_message["sent_from"]){
                        $message_client->send($message);
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn){
        $this->clients->detach($conn);
        $this->messageclients->detach($conn);
    }

    public function onError(ConnectionInterface $conn,\Exception $e){
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}