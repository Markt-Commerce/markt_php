<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

include_once "products.php";

use Markt\Product;

if(isset($_POST)){
    if(!empty($_POST["message"]) && !empty($_POST["buyer_id"]) && !empty($_POST["category"])){
        $product = new Product();
        if(is_string($_POST["category"])){
            $_POST["category"] = explode(",",$_POST["category"]);
            echo json_encode($product->create_product_query($_POST));
        }
    }
}

?>