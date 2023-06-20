<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'buyer.php';
require_once 'products.php';

use Markt\buyer;
use Markt\Product;

//remember to change $_POST back to $_COOKIE

$perfect_id = !empty($_POST["user_id"]) && is_string($_POST["user_id"]);

$perfect_user_type = !empty($_POST["user_type"]) && is_string($_POST["user_type"]);

if(isset($_POST) && $perfect_id && $perfect_user_type){
    if(!empty($_POST["product_id"]) && !empty($_POST["quantity"])){
        $product = new Product($_POST["product_id"]);
        if($_POST["quantity"] <= $product->product_quantity){
            $cart = [];
            $cart["quantity"] = $_POST["quantity"];
            $cart["product_id"] = $_POST["product_id"];
            $buyer = new buyer($_POST["user_id"]);
            echo json_encode($buyer->add_to_cart($cart));
        }
        else{
            echo json_encode("cannot add to cart as the quantity is larger than the product quantity");
        }
    }
}
else{
    echo json_encode("some parameters are not set");
}

?>