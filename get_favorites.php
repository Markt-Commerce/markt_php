<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'buyer.php';
require_once 'seller.php';
require_once 'products.php';

use Markt\buyer;
use Markt\Seller;
use Markt\Product;

$perfect_id = !empty($_GET["user_id"]) && is_string($_GET["user_id"]);

$perfect_user_type = !empty($_GET["user_type"]) && is_string($_GET["user_type"]);

if(isset($_GET) && $perfect_id && $perfect_user_type){
    $buyer = new buyer($_GET["user_id"]);
    if(count($buyer->favorites) > 0){
        $favorites = array();
        for($i = 0;$i < count($buyer->favorites);$i++){
            if($buyer->favorites[$i]["favorite_type"] == "seller"){
                $seller = new Seller($buyer->favorites[$i]["favorite_id"]);
                $favorites[$i]["favorite_type"] = "seller";
                $favorites[$i]["name"] = $seller->shopname;
                $favorites[$i]["profile_image"] = (empty($seller->profile_image)) ? "profile-placeholder-image" :$seller->profile_image;
                $favorites[$i]["favorite_id"] = $seller->get_seller_id(); 
            }
            elseif($buyer->favorites[$i]["favorite_type"] == "product"){
                $product = new Product($buyer->favorites[$i]["favorite_id"]);
                $favorites[$i]["favorite_type"] = "product";
                $favorites[$i]["name"] = $product->product_name;
                $favorites[$i]["favorite_id"] = $product->get_product_id();
                //$profile_image = $product->get_images()[0];
                $favorites[$i]["profile_image"] = (empty($product->get_images()[0])) ? "placeholder-image" : $product->get_images()[0]["image_name"];
            }
        }
        echo json_encode($favorites);
    }
    else{
        echo json_encode([]);
    }
}

?>