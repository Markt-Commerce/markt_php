<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "buyer.php";
require_once "seller.php";
require_once "delivery.php";

use Markt\Seller;
use Markt\delivery;
use Markt\buyer;

if(isset($_GET) && isset($_GET["user_type"]) && isset($_GET["user_id"])){
    switch($_GET["user_type"]){
        case "buyer":
            $buyer = new buyer($_GET["user_id"]);
            $buyer_details = $buyer->overview_summ();
            $buyer_details["payment"] = $buyer->buyer_payment_data_array;
            echo json_encode($buyer_details);
            break;
        case "seller":
            $seller = new Seller($_GET["user_id"]);
            $seller_details = $seller->overview_summ();
            $seller_details["category"] = explode(",",$seller->category);
            $seller_details["payment"] = $seller->seller_payment_data_array;
            echo json_encode($seller_details);
            break;
        case "delivery":
            $delivery = new delivery($_GET["user_id"]);
            $delivery_details = $delivery->overview_summ();
            $delivery_details["payment"] = $delivery->delivery_payment_data_array;
            echo json_encode($delivery_details);
            break;
        default:
            echo json_encode([]);
    }
}
?>