<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'buyer.php';
require_once 'products.php';

use Markt\buyer;
use Markt\Product;

$perfect_id = !empty($_COOKIE["user_id"]) && is_string($_COOKIE["user_id"]);

$perfect_user_type = !empty($_COOKIE["user_type"]) && is_string($_COOKIE["user_type"]);

if (isset($_POST) && $perfect_id && $perfect_user_type) {
    $buyer = new buyer($_COOKIE["user_id"]);
    $cart_items = $buyer->get_cart_items();
    $cart = array();
    for($i = 0;$i < count($cart_items);$i++){
        $cart[$i]["cart_id"] = $cart_items[$i]["cart_id"];
        $cart[$i]["quantity"] = $cart_items[$i]["quantity"];
        $product = new Product($cart_items[$i]["product_id"]);
        $cart[$i]["product_image"] = $product->get_images()[0];
        $cart[$i]["product_name"] = $product->product_name;
        $cart[$i]["product_type"] = $product->product_type;
        $cart[$i]["product_price"] = $product->product_price;
        $cart[$i]["product_id"] = $cart_items[$i]["product_id"];

    }
    echo json_encode($cart);
}
?>