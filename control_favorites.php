<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'buyer.php';
require_once 'seller.php';
require_once 'products.php';

use Markt\buyer;

if(isset($_POST) && !empty($_POST["control_type"]) && !empty($_POST["user_id"])){
    if(!empty($_POST["favorite_type"]) && !empty($_POST["favorite_id"])){
        $buyer = new buyer($_POST["user_id"]);
        if($_POST["control_type"] == "add"){
            $newfavorite = [];
            $newfavorite["favorite_type"] = $_POST["favorite_type"];
            $newfavorite["favorite_id"] = $_POST["favorite_id"];
            $buyer->add_favorite($newfavorite);
        }
        elseif($_POST["control_type"] == "remove"){
            echo json_encode($buyer->remove_favorite($_POST["favorite_id"]));
        }
    }
}


?>