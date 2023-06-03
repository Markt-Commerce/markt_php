<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";

use Markt\Product;

$product = new Product();
$product_image_position_flag = 0;

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
    for($i = 0 ;$i < count($_POST["products"]);$i++){
        $product_image = array();
        $product->desc_under = $_POST["products"][$i]["desc_under"];
        $product->estimated_size = $_POST["products"][$i]["estimated_size"];
        $product->product_category = $_POST["products"][$i]["product_category"];
        $product->product_description = $_POST["products"][$i]["product_description"];
        $product->product_name = $_POST["products"][$i]["product_name"];
        $product->product_price = $_POST["products"][$i]["product_price"];
        $product->product_quantity = $_POST["products"][$i]["product_quantity"];
        $product->product_type = $_POST["products"][$i]["product_type"];
        $product->seller_id = $_POST["seller_id"];
        for($j = $product_image_position_flag;$j < $product_image_position_flag + $_POST["products"][$i]["arrsize"]; $j++){
            $product_image[count($product_image)] = $_FILES[$j];
        }
        $product_image_position_flag += $_POST["products"][$i]["arrsize"];
        $product->create_product_images($product_image);
    }
    echo $product->create_product();
}

?>