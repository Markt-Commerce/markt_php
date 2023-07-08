<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "orders.php";

use Markt\orders\order;

if(isset($_POST) && !empty($_POST["user_type"]) && !empty($_POST["user_id"]) && !empty($_POST["order_id"])){
    if ($_POST["user_type"] == "seller") {
        $orders = new order();
        $unaccepted_orders = $orders->get_seller_non_accepted_orders($_POST["user_id"]);
        $declined = false;
        for($i = 0;$i < count($unaccepted_orders);$i++){
            if($unaccepted_orders[$i]["order_id"] == $_POST["order_id"]){
                $order = new order($_POST["order_id"]);
                $declined = $order->decline_order();
                break;
            }
        }
        echo json_encode($declined);
    }
    else{
        echo json_encode(false);
    }
}

?>