<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "buyer.php";
require_once "orders.php";

use Markt\buyer;
use Markt\orders\order;

if (isset($_GET) && !empty($_GET["user_id"]) && !empty($_GET["user_type"])) {
    if ($_GET["user_type"] = "buyer") {
        $buyer = new buyer($_GET["user_id"]);
        $order = new order();
        $buyer_unchecked_items = array();
        $buyer_unchecked_items["cart_item_number"] = $buyer->get_cart_item_number();
        $buyer_unchecked_items["order_item_number"] = $order->get_buyer_orders_amount($buyer->get_buyer_id());
        echo json_encode($buyer_unchecked_items);
    }
    elseif ($_GET["user_type"] = "seller") {
        $order = new order();
        $seller_unchecked_items = array();
        $seller_unchecked_items["unattended_order_item_number"] = $order->get_seller_unattended_order_amount($_POST["user_id"]);
        echo json_encode($seller_unchecked_items);
    }
}

?>