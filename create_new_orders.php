<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";
require_once "orders.php";
require_once "buyer.php";

use Markt\Product;
use Markt\orders\order;
use Markt\buyer;

if(isset($_POST) && !empty($_POST["user_id"]) && !empty($_POST["user_type"])){
    if($_POST["user_type"] = "buyer"){
        $successful_orders = [];
        $buyer = new buyer($_POST["user_id"]);
        $buyer_cart_items = $buyer->get_cart_items();
        foreach($buyer_cart_items as $cart_item){
            $product = new Product($cart_item["product_id"]);
            $order = new order();
            $order->product_id = $product->get_product_id();
            $order->seller_id = $product->seller_id;
            $order->buyer_id = $buyer->get_buyer_id();
            $order->product_quantity = $cart_item["quantity"];
            if($order->create_new_order()){
                $successful_order = [];
                $successful_order["product_name"] = $product->product_name;
                $successful_order["product_quantity"] = $cart_item["quantity"];
                $successful_order["product_price"] = $product->product_price;
                $successful_order["seller_id"] = $product->seller_id;
                array_push($successful_orders,$successful_order);
                $buyer->remove_from_cart($cart_item);
            }
        }
        echo json_encode($successful_orders);
    }
}

?>