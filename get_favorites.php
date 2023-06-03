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

$perfect_id = !empty($_COOKIE["user_id"]) && is_string($_COOKIE["user_id"]);

$perfect_user_type = !empty($_COOKIE["user_type"]) && is_string($_COOKIE["user_type"]);

if(isset($_POST) && $perfect_id && $perfect_user_type){
    $buyer = new buyer($_COOKIE["user_id"]);
    if(count($buyer->favorites) > 0){
        $favorites = array();
        for($i = 0;$i < count($buyer->favorites);$i++){
            if($buyer->favorites[$i]["favorite_type"] == "seller"){
                $seller = new Seller($buyer->favorites[$i]["favorite_id"]);
                $favorites[$i]["favorite_type"] = "seller";
                $favorites[$i]["name"] = $seller->shopname;
                $favorites[$i]["profile_image"] = $seller->profile_image; 
                $favorites[$i]["favorite_id"] = $seller->get_seller_id(); 
            }
            elseif($buyer->favorites[$i]["favorite_type"] == "product"){
                $product = new Product($buyer->favorites[$i]["favorite_id"]);
                $favorites[$i]["favorite_type"] = "product";
                $favorites[$i]["name"] = $product->product_name;
                $favorites[$i]["favorite_id"] = $product->get_product_id();
                $favorites[$i]["profile_image"] = $product->get_images()[0];
            }
        }
        echo json_encode($favorites);
    }
    else{
        echo json_encode([]);
    }
}

?>