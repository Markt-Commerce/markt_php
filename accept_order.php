<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "seller.php";
require_once "orders.php";

use Markt\orders\order;
use Markt\Seller;

if(isset($_POST) && isset($_POST["user_type"]) && isset($_POST["user_id"])){
    if(!empty($_POST["order_id"])){
        $seller = new Seller($_POST["user_id"]);
        $order = new order($_POST["order_id"]);
        if($order->seller_id == $_POST["user_id"]){
            echo json_encode($order->accept_order());
        }
        else{
            echo json_encode("this seller does not have the permission to perform this action");
        }
    }
    else{
        echo json_encode("order not available or does not exist");
    }
}
else{
    echo json_encode("parameters not set");
}

?>