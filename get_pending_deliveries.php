<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "delivery.php";
require_once "buyer.php";
require_once "seller.php";
require_once "products.php";

use Markt\delivery;
use Markt\buyer;
use Markt\Product;
use Markt\Seller;

if(isset($_GET) && isset($_GET["user_type"]) && isset($_GET["user_id"])){
    if($_GET["user_type"] == "delivery"){
        $delivery = new delivery($_GET["user_id"]);
        $delivery_orders = $delivery->get_pending_deliveries();
        for ($i=0; $i < count($delivery_orders); $i++) { 
            $delivery_order = array();
            $buyer = new buyer($delivery_orders[$i]["buyer_id"]);
            $delivery_orders[$i]["buyer_name"] = $buyer->username;
            $seller = new Seller($delivery_orders[$i]["seller_id"]);
            $delivery_orders[$i]["seller_name"] = $seller->shopname;
            $product = new Product($delivery_orders[$i]["product_id"]);
            $delivery_orders[$i]["product_name"] = $product->product_name;
            $delivery_orders[$i]["product_size"] = $product->estimated_size;
            $product_images = $product->get_images();
            $delivery_orders[$i]["product_image"] = (count($product_images) == 0) ? "placeholder-image" : $product_images[0]["image_name"];
        }
        echo json_encode($delivery_orders);
    }
}
?>