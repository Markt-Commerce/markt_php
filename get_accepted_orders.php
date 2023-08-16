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


if(isset($_GET) && isset($_GET["user_type"]) && isset($_GET["user_id"])){
    $seller = new Seller($_GET["user_id"]);
    $order = new order();
    $order->seller_id = $_GET["user_id"];
    $accepted_orders =  $order->get_seller_accepted_orders($_GET["user_id"]);
    for($i = 0;$i < count($accepted_orders);$i++){
        $displayed_order = array();
        $displayed_order["order_id"] = $accepted_orders[$i]["order_id"];
        $displayed_order["seller_id"] = $accepted_orders[$i]["seller_id"];
        $displayed_order["product_quantity"] = $accepted_orders[$i]["product_quantity"];
        $displayed_order["order_date"] = $accepted_orders[$i]["order_date"];
        $displayed_order["has_discount"] = $accepted_orders[$i]["has_discount"];
        $displayed_order["discount_percent"] = $accepted_orders[$i]["discount_percent"];
        $displayed_order["discount_price"] = $accepted_orders[$i]["discount_price"];
        $product = new Product($accepted_orders[$i]["product_id"]);
        $displayed_order["product_name"] = $product->product_name;
        $displayed_order["product_price"] = $product->product_price;
        $displayed_order["product_id"] = $product->get_product_id();
        $displayed_order["product_image"] = $product->get_images();
        //a placeholder image needs to be created and added here incase a product does
        //not have an image
        $displayed_order["product_image"] = (!empty($displayed_order["product_image"][0]["image_name"])) ? $displayed_order["product_image"][0]["image_name"] : "placeholder-image";
        $buyer = new buyer($accepted_orders[$i]["buyer_id"]);
        $displayed_order["buyer_id"] = $buyer->get_buyer_id();
        $displayed_order["buyer_name"] = $buyer->username;
        $accepted_orders[$i] = $displayed_order;
    }
    echo json_encode($accepted_orders);
}

?>