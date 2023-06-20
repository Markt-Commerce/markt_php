<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "products.php";
require_once "seller.php";

use Markt\Product;
use Markt\Seller;

function arrange_and_send_products($products){
    for($i = 0; $i < count($products);$i++){
        $new_product = new Product($products[$i]["product_id"]);
        $products[$i]["product_images"] = $new_product->get_images();
        if(count($products[$i]["product_images"]) == 0){
            $products[$i]["product_images"][0] = "placeholder-image";
        }
        else{
            for($j = 0; $j < count($products[$i]["product_images"]); $j++){
                $products[$i]["product_images"][$j] = $products[$i]["product_images"][$j]["image_name"];
            }
        }
    }
    echo json_encode($products);
}

if(isset($_GET) && isset($_GET["type"])){
    $product = new Product();
    switch($_GET["type"]){
        case "single":
            try{
                $spec_product = new Product($_GET["product_id"]);
                $product = $spec_product->overview_summ();
                $seller = new Seller($spec_product->seller_id);
                $product["seller_name"] = $seller->shopname;
                $product["product_images"] = $spec_product->get_images();
                if(count($product["product_images"]) == 0){
                    $product["product_images"][0] = "placeholder-image";
                }
                else{
                    for($j = 0; $j < count($product["product_images"]); $j++){
                        $product["product_images"][$j] = $product["product_images"][$j]["image_name"];
                    }
                }
                echo json_encode($product);
            }catch(\Exception $e){
                echo json_encode($e);
            }
            break;
        case "getrandom":
            try{
                $products = $product->get_products($_GET["amount"]);
                arrange_and_send_products($products);
            }catch(\Exception $e){
                echo json_encode($e);
            }
            break;
        case "get_random":
            try{
                $products = $product->get_products($_GET["amount"]);
                arrange_and_send_products($products);
            }catch(\Exception $e){
                echo json_encode($e);
            }
            break;
        case "search":
            $products = $product->search_products_in_packets($_GET["product_name"],$_GET["start_idx"]);
            arrange_and_send_products($products);
            break;
        case "sellerproducts":
            $products = $product->get_products_belonging_to_seller($_GET["seller_id"]);
            arrange_and_send_products($products);
            break;
        case "seller_products":
            $products = $product->get_products_belonging_to_seller($_GET["seller_id"]);
            arrange_and_send_products($products);
            break;
        case "category_and_search":
            $products =  $product->search_products_in_packets(
                                                        $_GET["product_name"],
                                                        $_GET["start_idx"],
                                                        $_GET["product_category"]);
            arrange_and_send_products($products);
            break;
        case "category":
            $products = $product->get_products_from_category_in_packets($_GET["product_category"]);
            arrange_and_send_products($products);
            break;
        default:
            echo json_encode([]);
            break;
    }
}