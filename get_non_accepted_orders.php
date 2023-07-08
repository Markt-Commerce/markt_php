<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "seller.php";
require_once "orders.php";
require_once "products.php";
require_once "buyer.php";

use Markt\orders\order;
use Markt\Seller;
use Markt\Product;
use Markt\buyer;

//make sure to change $_GET["user_type"] and $_GET["user_id"] back to $_COOKIE["user_type"] and $_COOKIE["user_id"]
if(isset($_GET) && isset($_GET["user_type"]) && isset($_GET["user_id"])){
    $seller = new Seller($_GET["user_id"]);
    $order = new order();
    $order->seller_id = $_GET["user_id"];
    $unaccepted_orders = $order->get_seller_non_accepted_orders($_GET["user_id"]);
    for($i = 0;$i < count($unaccepted_orders);$i++){
        $displayed_order = array();
        $displayed_order["order_id"] = $unaccepted_orders[$i]["order_id"];
        $displayed_order["seller_id"] = $unaccepted_orders[$i]["seller_id"];
        $displayed_order["product_quantity"] = $unaccepted_orders[$i]["product_quantity"];
        $displayed_order["order_date"] = $unaccepted_orders[$i]["order_date"];
        $product = new Product($unaccepted_orders[$i]["product_id"]);
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
        $buyer = new buyer($unaccepted_orders[$i]["buyer_id"]);
        $displayed_order["buyer_id"] = $buyer->get_buyer_id();
        $displayed_order["buyer_name"] = $buyer->username;
        $unaccepted_orders[$i] = $displayed_order;
    }
    echo json_encode($unaccepted_orders);
}

?>