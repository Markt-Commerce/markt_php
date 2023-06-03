<?php

namespace Markt\DB;

/** 
 * classs for interacting with the chat database,
 * writes chats, created new chats in the database,
 * updates and deletes chats
*/
class chatDB{
    
    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";

    private $conn;

    private $query_chat;


    /**
     * crates a new connection to the chat database
     * creates the chat table if it does not exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if (!$this->conn) {
            
        }
        else{
            $this->query_chat = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS chats 
            (id INT NOT NULL AUTO_INCREMENT , message_id VARCHAR(400) NOT NULL , 
            sent_to VARCHAR(400) NOT NULL , attached_file VARCHAR(400) NOT NULL , 
            message VARCHAR(400) NOT NULL , send_date_and_time DATETIME NOT NULL , 
            status VARCHAR(255) NOT NULL , sent_from VARCHAR(400) NOT NULL , 
            PRIMARY KEY (`id`))");
        }
    }

    /**
     * creates a new chat in the chat table of
     * the database
     * @param array $Chat an rray containing the chat details
     * @return boolean
     */
    public function create_chat($Chat)
    {
        $Chat["message_id"] = mysqli_real_escape_string($this->conn,$Chat["message_id"]);
        $Chat["sent_to"] = mysqli_real_escape_string($this->conn,$Chat["sent_to"]);
        $Chat["attached_file"] = mysqli_real_escape_string($this->conn,$Chat["attached_file"]);
        $Chat["message"] = mysqli_real_escape_string($this->conn,$Chat["message"]);
        $Chat["send_date_and_time"] = mysqli_real_escape_string($this->conn,$Chat["send_date_and_time"]);
        $Chat["status"] = mysqli_real_escape_string($this->conn,$Chat["status"]);
        $Chat["sent_from"] = mysqli_real_escape_string($this->conn,$Chat["sent_from"]);
        return mysqli_query($this->conn,"INSERT INTO chats(
            message_id, sent_to, attached_file, message, 
            send_date_and_time, status, sent_from) 
            VALUES ('{$Chat["message_id"]}','{$Chat["sent_to"]}',
            '{$Chat["attached_file"]}','{$Chat["message"]}','{$Chat["send_date_and_time"]}',
            '{$Chat["status"]}','{$Chat["sent_from"]}')");
    }

    /**
     * gets all available chats in the database
     * returns an array containing the data
     * @return array
     */
    public function get_all_chats(){
        $query = mysqli_query($this->conn,"SELECT * FROM chats WHERE 1");
        $all_chats = mysqli_fetch_all($query,MYSQL_ASSOC);
        return $all_chats;
    }

    /**
     * gets a specific chat from the database using its id
     * returns an associative array
     * @param int $messsage_id the id of the chat to be deleted
     * @return array
     */
    public function get_chat($message_id){
        $message_id = mysqli_real_escape_string($this->conn,$message_id);
        $query = mysqli_query($this->conn,"SELECT * FROM chats WHERE message_id = '{$message_id}'");
        $chat = mysqli_fetch_assoc($query);
        return $chat;
    }

    /**
     * gets all chats connected to a sender id
     * returns an associative array
     * @param int $sender_id the id of the sender 
     * @return array
     */
    public function get_chat_using_sender_id($Sender_id){
        $Sender_id = mysqli_real_escape_string($this->conn,$Sender_id);
        $query = mysqli_query($this->conn,"SELECT * FROM chats WHERE sent_from = '{$Sender_id}' 
                                                        OR sent_to = '{$Sender_id}'");
        $chats = mysqli_fetch_all($query,MYSQL_ASSOC);
        return $chats;
    }

    /**
     * gets all chats sent on a particular date
     * returns an associative array
     * @param string $Date the id of the sender of the message
     * @return array
     */
    public function get_chat_using_date($Date){
        $Date = mysqli_real_escape_string($this->conn,$Date);
        $query = mysqli_query($this->conn,"SELECT * FROM chats 
                                    WHERE send_date_and_time = '{$Date}'");
        $chats = mysqli_fetch_all($query,MYSQL_ASSOC);
        return $chats;
    }

    /**
     * deletes a chat using its id
     * @param int $messsage_id the id of the chat to be deleted
     * @return boolean
     */
    public function delete_chat($message_id){
        $message_id = mysqli_real_escape_string($this->conn,$message_id);
        return mysqli_query($this->conn,"DELETE FROM chats WHERE message_id = '{$message_id}'");
    }
}

?>