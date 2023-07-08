<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

include_once "chat.php";
include_once "buyer.php";
include_once "seller.php";
include_once "delivery.php";

use Markt\Chat;
use Markt\Seller;
use Markt\buyer;
use Markt\delivery;

if(isset($_GET) && !empty($_GET["user_id"])){
    $chat = new Chat();
    $chat->sent_from = $_GET["user_id"];
    $allchats = $chat->get_chat();
    for ($i=0; $i < count($allchats); $i++) { 
        if(str_contains($allchats[$i]["user_id"],"seller")){
            $seller = new Seller($allchats[$i]["user_id"]);
            $allchats[$i]["user_name"] = $seller->shopname;
            $allchats[$i]["user_profile_image"] = (!str_contains($seller->profile_image,"image") || empty($seller->profile_image)) ? "placeholder-image" : $seprofile_image;
            $allchats[$i]["user_type"] = "Seller";
        }
        elseif(str_contains($allchats[$i]["user_id"],"buyer")){
            $buyer = new buyer($allchats[$i]["user_id"]);
            $allchats[$i]["user_name"] = $buyer->username;
            $allchats[$i]["user_profile_image"] = (!str_contains($buyer->profile_image,"image") || empty($buyer->profile_image)) ? "placeholder-image" : $buyer->profile_image;
            $allchats[$i]["user_type"] = "Buyer";
        }
        elseif(str_contains($allchats[$i]["user_id"],"delivery")){
            $delivery = new delivery($allchats[$i]["user_id"]);
            $allchats[$i]["user_name"] = $delivery->deliveryname;
            $allchats[$i]["user_profile_image"] = (!str_contains($delivery->profile_image,"image") || empty($delivery->profile_image)) ? "placeholder-image" : $delivery->profile_image;
            $allchats[$i]["user_type"] = "Delivery";
        }
    }
    echo json_encode($allchats);
}

?>