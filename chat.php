<?php

namespace Markt;
include_once "dbconnections/chat_database_connections.php";
include_once "image_handler.php";
include_once "definitions.php";

use Markt\DB\chatDB;
use Markt\ImageHandler;

/**
 * A class for handling chats and messages
 */
class Chat{

    /**
     * The unique id of the chat used to identify it in the database
     */
    private $message_id; 

    /**
     * the id of the user the message was sent to
     */
    public $sent_to;

    /**
     * any file sent, traditionally null if message is not null 
     * and is the name of the file when a file is sent. When a file is sent,
     * message would become null
     */
    public $attached_file;

    /**
     * the chat message, the message is only null if 
     * the incoming chat has a file type
     */
    public $message;

    /**
     * the time and date the chat was sent. Is usually in the 
     * format `YY/MM/DD  HH:MM:SS`  
     */
    public $send_date_and_time; 

    /**
     * the status of the message if it was sent or if it was not
     */
    public $status;

    /**
     * the id of the user that sent the message
     */
    public $sent_from;

    /**
     * a connection to the chat database to store the chat information
     */
    private $connection_to_db;

    /**
     * 
     */
    public function __construct($Chatdata = null) {
        $this->connection_to_db = new chatDB();
        if (is_array($Chatdata)) {
            $this->message = $Chatdata["message"];
            $this->sent_from = $Chatdata["sent_from"];
            $this->attached_file = $Chatdata["attached_file"];
            if (!is_null($this->message) && !empty($this->message) && isset($this->message)) {
                $this->attached_file = "";
            }
        }
    }

    /**
     * gets chats connected to a particular user using the 
     * user's id. returns undefined if the user id is undefined 
     * set the user id using by creating a new instance of Chat 
     * and setting `new_instace_of_class->sent_from` to the id of 
     * the user. Gets all messages sent from and to the user using 
     * `new_instace_of_class->sent_from`
     * @return array|void
     */
    public function get_chat(){
        if(empty($this->sent_from)){
            return;
        }
        $all_chats = $this->connection_to_db->get_chat_using_sender_id($this->sent_from);
        return $this->arrange_chats($all_chats);
    }

    /**
     * arrange chats by setting chats belonging to the same user together
     * @param array $chat_bundle
     * @return array
     */
    private function arrange_chats($chat_bundle){
        $arranged_chats = array();
        for($i = 0; $i < count($chat_bundle); $i++){
            $chat = $chat_bundle[$i];
            $added = false;
            if ($chat["sent_from"] == $this->sent_from) {
                for ($j=0; $j < count($arranged_chats); $j++) { 
                    if ($chat["sent_to"] == $arranged_chats[$j]["user_id"]) {
                        array_push($arranged_chats[$j]["messages"],$chat);
                        $added = true;
                        break;
                    }
                }
                if(!$added){
                    $new_chat = array();
                    $new_chat["user_id"] = $chat["sent_to"];
                    $new_chat["messages"] = array();
                    array_push($new_chat["messages"],$chat);
                    array_push($arranged_chats,$new_chat);
                }
            }
            elseif ($chat["sent_to"] == $this->sent_from) {
                for ($j=0; $j < count($arranged_chats); $j++) { 
                    if ($chat["sent_from"] == $arranged_chats[$j]["user_id"]) {
                        array_push($arranged_chats[$j]["messages"],$chat);
                        $added = true;
                        break;
                    }
                }
                if(!$added){
                    $new_chat = array();
                    $new_chat["user_id"] = $chat["sent_from"];
                    $new_chat["messages"] = array();
                    array_push($new_chat["messages"],$chat);
                    array_push($arranged_chats,$new_chat);
                }
            }
        }
        return $arranged_chats;
    }

    /**
     * @return string
     */
    public function handle_chat_file($file){
        $imagehandler = new ImageHandler();
        return $imagehandler->upload_chat_image($file);
    }


    /**
     * Sends a message to a user using the receiver id
     * @param string $receiver_id the id of the user the message is being sent to
     * @return bool
     */
    public function send_message($receiver_id){
        $chat = array();
        $this->message_id = uniqid("chat-",true);
        $this->sent_to = $receiver_id;
        $this->send_date_and_time = date('Y-m-d H:i:s');
        $chat["message_id"] = $this->message_id;
        $chat["sent_to"] = $receiver_id;
        $chat["attached_file"] = $this->handle_chat_file($this->attached_file);
        $chat["message"] = $this->message;
        $chat["send_date_and_time"] = $this->send_date_and_time;
        $chat["status"] = "sent";
        $chat["sent_from"] = $this->sent_from;

        return $this->connection_to_db->create_chat($chat);
    }

    /**
     * Deletes a message from the database
     * @param string mixed $user_id
     * @param array $chat
     * @return bool
     */
    public function delete_message($user_id,$chat){
        if($user_id = $chat["sent_from"])
            return $this->connection_to_db->delete_chat($chat["message_id"]);
        return false;
    } 

}

?>