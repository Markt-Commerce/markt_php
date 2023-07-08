<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

include_once "buyer.php";
include_once "seller.php";
include_once "delivery.php";

use Markt\Seller;
use Markt\buyer;
use Markt\delivery;

if(isset($_POST) && !empty($_POST["user_id"]) && !empty($_POST["user_type"])){
    if (!empty($_FILES["profile_image"])) {
        switch ($_POST["user_type"]) {
            case 'buyer':
                $buyer = new buyer($_POST["user_id"]);
                echo json_encode($buyer->update_buyer_profile_image($_FILES["profile_image"]));
                break;
            case 'seller':
                $seller = new Seller($_POST["user_id"]);
                echo json_encode($seller->update_seller_profile_image($_FILES["profile_image"]));
                break;
            case 'delivery':
                $delivery = new delivery($_POST["user_id"]);
                echo json_encode($delivery->update_delivery_profile_image($_FILES["profile_image"]));
                break;
            default:
                echo json_encode(false);
                break;
        }
    }
}

?>