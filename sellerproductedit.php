<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";

use Markt\Product;

if(isset($_POST) && isset($_POST["seller_id"]) && isset($_POST["product_id"])){
    $column_names = [];
    $values = [];
    $product = new Product($_POST["product_id"]);
    if ($product->desc_under != $_POST["desc_under"]) {
        array_push($column_names,"desc_under");
        array_push($values,$_POST["desc_under"]);
    }
    if ($product->estimated_size != $_POST["estimated_size"]) {
        array_push($column_names,"estimated_size");
        array_push($values,$_POST["estimated_size"]);
    }
    if ($product->product_category != $_POST["product_category"]) {
        array_push($column_names,"product_category");
        array_push($values,$_POST["product_category"]);
    }
    if ($product->product_description != $_POST["product_description"]) {
        array_push($column_names,"product_description");
        array_push($values,$_POST["product_description"]);
    }
    if ($product->product_name != $_POST["product_name"]) {
        array_push($column_names,"product_name");
        array_push($values,$_POST["product_name"]);
    }
    if ($product->product_price != $_POST["product_price"]) {
        array_push($column_names,"product_price");
        array_push($values, $_POST["product_price"]);
    }
    if ($product->product_quantity != $_POST["product_quantity"]) {
        array_push($column_names,"product_quantity");
        array_push($values,$_POST["product_quantity"]);
    }
    if ($product->product_type != $_POST["product_type"]) {
        array_push($column_names,"product_type");
        array_push($values,$_POST["product_type"]);
    }
    $product->add_new_product_images($_FILES);
    $product->add_tags(json_decode($_POST["tags"],true));
    $product->save_tags();
    echo json_encode($product->update_multiple_product(null,$column_names,$values));
}


?>