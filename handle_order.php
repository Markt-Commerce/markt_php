<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "orders.php";

use Markt\orders\order;

if(isset($_POST) && !empty($_POST["user_id"]) && !empty($_POST["user_type"]) && !empty($_POST["order_id"])){
    if($_POST["user_type"] == "delivery"){
        $order = new order($_POST["order_id"]);
        echo json_encode($order->assign_delivery($_POST["user_id"]));
    }
}

?>