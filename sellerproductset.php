<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";

use Markt\Product;

$product = new Product();

/**
 * Format of $_POST:
 * $_POST[
 *          seller_id(string),
 *          products[
 *                  product_images
 * ]
 * ]
 */
if(isset($_POST) && isset($_POST["seller_id"])){
    $product_image = array();
    $product->desc_under = $_POST["desc_under"];
    $product->estimated_size = $_POST["estimated_size"];
    $product->product_category = $_POST["product_category"];
    $product->product_description = $_POST["product_description"];
    $product->product_name = $_POST["product_name"];
    $product->product_price = $_POST["product_price"];
    $product->product_quantity = $_POST["product_quantity"];
    $product->product_type = $_POST["product_type"];
    $product->seller_id = $_POST["seller_id"];
    $product->this_products_images = $_FILES;
    $product->add_tags(json_decode($_POST["tags"],true));
    echo json_encode($product->create_product());
}


?>