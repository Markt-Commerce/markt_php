<?php
use Markt\Product;

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "delivery.php";
require_once "buyer.php";
require_once "seller.php";

use Markt\delivery;
use Markt\buyer;
use Markt\Seller;

/**
 * Gets the delivery orders. The delivery person has to set if he wants to use his present location and
 * the effects of not doing so and if he dos not want to use his present location. If the delivery sets
 * the location, the orders would be gotten from the database using the new location that was sent. If
 * the delivery does not set the location, the last location saved in the database would be used 
 * instead. Although it is very important that delivery location is set.
 */

if(isset($_GET) && !empty($_GET["user_id"]) && !empty($_GET["user_type"])){
    if($_GET["user_type"] == "delivery"){
        if(!empty($_GET["longtitude"]) && !empty($_GET["latitude"])){
            $delivery = new delivery($_GET["user_id"]);
            $delivery->longtitude = $_GET["longtitude"];
            $delivery->latitude = $_GET["latitude"];
            $delivery_orders = $delivery->get_delivery_orders();
            for ($i=0; $i < count($delivery_orders); $i++) { 
                $delivery_order = array();
                $buyer = new buyer($delivery_orders[$i]["buyer_id"]);
                $delivery_orders[$i]["buyer_name"] = $buyer->username;
                $seller = new seller($delivery_orders[$i]["seller_id"]);
                $delivery_orders[$i]["seller_name"] = $seller->shopname;
                $product = new Product($delivery_orders[$i]["product_id"]);
                $delivery_orders[$i]["product_name"] = $product->product_name;
                $delivery_orders[$i]["product_size"] = $product->estimated_size;
                $product_images = $product->get_images();
                if(count($product_images) == 0){
                    $delivery_orders[$i]["product_image"] = "placeholder-image";
                }
                else{
                    $delivery_orders[$i]["product_image"] = $product_images[0]["image_name"];
                }
            }
            echo json_encode($delivery_orders);
        }
        else{
            $delivery = new delivery($_GET["user_id"]);
            $delivery_orders = $delivery->get_delivery_orders();
            for ($i=0; $i < count($delivery_orders); $i++) { 
                $delivery_order = array();
                $buyer = new buyer($delivery_orders[$i]["buyer_id"]);
                $delivery_orders[$i]["buyer_name"] = $buyer->username;
                $seller = new seller($delivery_orders[$i]["seller_id"]);
                $delivery_orders[$i]["seller_name"] = $seller->shopname;
                $product = new Product($delivery_orders[$i]["product_id"]);
                $delivery_orders[$i]["product_name"] = $product->product_name;
                $delivery_orders[$i]["product_size"] = $product->estimated_size;
                $product_images = $product->get_images();
                if(count($product_images) == 0){
                    $delivery_orders[$i]["product_image"] = "placeholder-image";
                }
                else{
                    $delivery_orders[$i]["product_image"] = $product_images[0]["image_name"];
                }
            }
            echo json_encode($delivery_orders);
        }
    }
    else{
        echo json_encode("wrong user type");
    }
}