<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "buyer.php";
require_once "seller.php";
require_once "delivery.php";
require_once "orders.php";
require_once "products.php";

use Markt\Seller;
use Markt\delivery;
use Markt\orders\order;
use Markt\buyer;
use Markt\Product;

if(isset($_GET) && !empty($_GET["user_id"]) && !empty($_GET["user_type"])){
    $order = new order();
    $buyer = new buyer($_GET["user_id"]);
    $buyer_orders = $order->get_buyer_orders($buyer->get_buyer_id());
    for($i = 0;$i < count($buyer_orders);$i++){
        $displayed_order = array();
        $displayed_order["order_id"] = $buyer_orders[$i]["order_id"];
        if(empty($buyer_orders[$i]["seller_id"])){
            $displayed_order["seller_id"] = "";
            $displayed_order["seller_shopname"] = "";
        }
        else{
            $seller = new Seller($buyer_orders[$i]["seller_id"]);
            $displayed_order["seller_id"] = $seller->get_seller_id();
            $displayed_order["seller_shopname"] = $seller->shopname;
        }
        $displayed_order["product_quantity"] = $buyer_orders[$i]["product_quantity"];
        $displayed_order["order_date"] = $buyer_orders[$i]["order_date"];
        $displayed_order["declined"] = empty($buyer_orders[$i]["seller_id"]) ? true : false;
        $displayed_order["accepted"] = boolval($buyer_orders[$i]["accepted"]);
        $displayed_order["received_by_delivery"] = boolval($buyer_orders[$i]["received_by_delivery"]);
        $displayed_order["has_discount"] = $buyer_orders[$i]["has_discount"];
        $displayed_order["discount_percent"] = $buyer_orders[$i]["discount_percent"];
        $displayed_order["discount_price"] = $buyer_orders[$i]["discount_price"];
        if(empty($buyer_orders[$i]["delivery_id"])){
            $displayed_order["delivery_name"] = "";
            $displayed_order["delivery_id"] = "";
        }
        else{
            $delivery = new delivery($buyer_orders[$i]["delivery_id"]);
            $displayed_order["delivery_name"] = $delivery->deliveryname;
            $displayed_order["delivery_id"] = $delivery->get_delivery_id();
        }
        $product = new Product($buyer_orders[$i]["product_id"]);
        $displayed_order["product_name"] = $product->product_name;
        $displayed_order["product_price"] = $product->product_price;
        $displayed_order["product_id"] = $product->get_product_id();
        $displayed_order["product_image"] = $product->get_images();
        if(!empty($displayed_order["product_image"][0]["image_name"])){
            $displayed_order["product_image"] = $displayed_order["product_image"][0]["image_name"];
        }
        else{
            //a placeholder image needs to be created and added here incase a product does
            //not have an image
            $displayed_order["product_image"] = "placeholder-image";
        }
        $buyer_orders[$i] = $displayed_order;
    }
    echo json_encode($buyer_orders);
}

?>