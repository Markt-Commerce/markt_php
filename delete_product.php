<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";

use Markt\Product;

if(isset($_POST) && !empty($_POST["seller_id"]) && !empty($_POST["product_id"])){
    $product = new Product($_POST["product_id"]);
    if($product->seller_id == $_POST["seller_id"]){
        echo json_decode($product->delete_product());
    }
}

?>