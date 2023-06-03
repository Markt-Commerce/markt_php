<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";

use Markt\Product;

if(isset($_GET) && isset($_GET["type"])){
    $product = new Product();
    switch($_GET["type"]){
        case "getrandom":
            try{
                $products = $product->get_products($_GET["amount"]);
                for($i = 0; $i < count($products);$i++){
                    $new_product = new Product($products[$i]["product_id"]);
                    $products[$i]["product_images"] = $new_product->get_images();
                    for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                        $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                    }
                }
                echo json_encode($products);
            }catch(\Exception $e){
                echo json_encode($e);
            }
            break;
        case "get_random":
            try{
                $products = $product->get_products($_GET["amount"]);
                for($i = 0; $i < count($products);$i++){
                    $new_product = new Product($products[$i]["product_id"]);
                    $products[$i]["product_images"] = $new_product->get_images();
                    for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                        $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                    }
                }
                echo json_encode($products);
            }catch(\Exception $e){
                echo json_encode($e);
            }
            break;
        case "search":
            $products = $product->search_products_in_packets($_GET["product_name"],$_GET["start_idx"]);
            for($i = 0; $i < count($products);$i++){
                $new_product = new Product($products[$i]["product_id"]);
                $products[$i]["product_images"] = $new_product->get_images();
                for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                    $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                }
            }
            echo json_encode($products);
            break;
        case "sellerproducts":
            $products = $product->get_products_belonging_to_seller($_GET["seller_id"]);
            for($i = 0; $i < count($products);$i++){
                $new_product = new Product($products[$i]["product_id"]);
                $products[$i]["product_images"] = $new_product->get_images();
                for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                    $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                }
            }
            echo json_encode($products);
            break;
        case "seller_products":
            $products = $product->get_products_belonging_to_seller($_GET["seller_id"]);
            for($i = 0; $i < count($products);$i++){
                $new_product = new Product($products[$i]["product_id"]);
                $products[$i]["product_images"] = $new_product->get_images();
                for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                    $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                }
            }
            echo json_encode($products);
            break;
        case "category_and_search":
            $products =  $product->search_products_in_packets(
                                                        $_GET["product_name"],
                                                        $_GET["start_idx"],
                                                        $_GET["product_category"]);
            for($i = 0; $i < count($products);$i++){
                $new_product = new Product($products[$i]["product_id"]);
                $products[$i]["product_images"] = $new_product->get_images();
                for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                    $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                }
            }
            echo json_encode($products);
            break;
        case "category":
            $products = $product->get_products_from_category_in_packets($_GET["product_category"]);
            for($i = 0; $i < count($products);$i++){
                $new_product = new Product($products[$i]["product_id"]);
                $products[$i]["product_images"] = $new_product->get_images();
                for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                    $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
                }
            }
            echo json_encode($products);
            break;
        default:
            echo json_encode([]);
            break;
    }
}