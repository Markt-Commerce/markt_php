<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require "seller.php";
require "order.php";

use Markt\orders\order;
use Markt\Seller;

if(isset($_GET) && isset($_GET["user_type"]) && isset($_GET["user_id"])){
    $seller = new Seller($_GET["user_id"]);
    $order = new order();
    $order->seller_id = $_GET["user_id"];
    echo json_encode($order->get_seller_accepted_orders($_GET["user_id"]));
}

?>