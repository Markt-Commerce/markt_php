<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "comments.php";
require_once "seller.php";
require_once "products.php";

use Markt\Comment;
use Markt\Seller;
use Markt\Product;

if(isset($_GET) && !empty($_GET["comment_place_id"]) && !empty($_GET["comment_place"])){
    $feedbackdata = array();
    $comment = new Comment();
    $feedbackdata["comments"] = $comment->get_comments($_GET["comment_place_id"],$_GET["comment_place"]);
    if($_GET["comment_place"] == "Product"){
        $product = new Product($_GET["comment_place_id"]);
        if(isset($product->seller_id)){
            $seller = new Seller($product->seller_id);
            $feedbackdata["rating"] = $seller->seller_rating;
        }
    }
    elseif ($_GET["comment_place"] == "Seller") {
        $seller = new Seller($_GET["comment_place_id"]);
        $feedbackdata["rating"] = $seller->seller_rating;
    }
    echo json_encode($feedbackdata);
}

?>