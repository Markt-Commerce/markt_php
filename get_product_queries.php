<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

include_once "products.php";
include_once "seller.php";
include_once "buyer.php";

use Markt\Product;
use Markt\Seller;
use Markt\buyer;

function initbuyer($queries,$buyer_id = null){
    if (!is_null($buyer_id)) {
        $buyer = new buyer($buyer_id);
        for($i =0;$i < count($queries);$i++){
            $queries[$i]["buyer_name"] = $buyer->username;
            $queries[$i]["city"] = $buyer->city;
            $queries[$i]["state"] = $buyer->state;
            $queries[$i]["profile_image"] = (empty($buyer->profile_image)) ? "profile-placeholder-image" : $buyer->profile_image;
        }
        return $queries;
    }
    for($i =0;$i < count($queries);$i++){
        $buyer =  new buyer($queries[$i]["buyer_id"]);
        $queries[$i]["buyer_name"] = $buyer->username;
        $queries[$i]["city"] = $buyer->city;
        $queries[$i]["state"] = $buyer->state;
        $queries[$i]["profile_image"] = (empty($buyer->profile_image)) ? "profile-placeholder-image" : $buyer->profile_image;
    }
    return $queries;
}

if(isset($_GET) && !empty($_GET["type"])){
    $product = new Product();
    switch($_GET["type"]){
        case "buyer":
            if(!empty($_GET["buyer_id"])){
                echo json_encode(initbuyer(
                    $product->get_buyer_product_queries($_GET["buyer_id"]),$_GET["buyer_id"]));
            }
            break;
        case "category":
            if(!empty($_GET["categories"])){
                $categories = explode("+",$_GET["categories"]);
                echo json_encode(initbuyer(
                    $product->get_product_query_by_category($categories)));
            }
            break;
        case "seller":
            if(!empty($_GET["seller_id"])){
                $seller = new Seller($_GET["seller_id"]);
                $category = $seller->category;
                $categories = explode(",",$category);
                echo json_encode(initbuyer(
                    $product->get_product_query_by_category($categories)));
            }
            break;
    }
}

?>