<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'buyer.php';

use Markt\buyer;

$perfect_id = !empty($_COOKIE["user_id"]) && is_string($_COOKIE["user_id"]);

$perfect_user_type = !empty($_COOKIE["user_type"]) && is_string($_COOKIE["user_type"]);

if(isset($_POST) && $perfect_id && $perfect_user_type){
    $buyer = new buyer($_COOKIE["user_id"]);
    if(count($buyer->get_buyer($_COOKIE["user_id"])) <= 0){
        if(!empty($favorite["favorite_id"]) && !empty($favorite["favorite_type"])){
            $favorite = array();
            $favorite["favorite_id"] = $_POST["favorite_id"];
            $favorite["favorite_type"] = $_POST["favorite_type"]; 
            echo json_encode($buyer->add_favorite($favorite));
        }
    }
}

?>