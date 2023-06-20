<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "buyer.php";
require_once "seller.php";
require_once "delivery.php";

use Markt\Seller;
use Markt\delivery;
use Markt\buyer;

if(isset($_POST) && !empty($_POST["user_type"]) && !empty($_POST["user_id"])){
    if($_POST["user_type"] == "buyer"){
        $buyer = new buyer($_POST["user_id"]);
        $buyer_data = json_decode($_POST["buyer_data"],true);
        if(!empty($buyer_data["username"]) && $buyer_data["username"] != $buyer->username){
            $buyer->update_buyer_detail("username",$buyer_data["username"]);
        }
        if(!empty($buyer_data["phone_number"]) && $buyer_data["phone_number"] != $buyer->phone_number){
            $buyer->update_buyer_detail("phone_number",$buyer_data["phone_number"]);
        }
        if(!empty($buyer_data["house_number"]) && $buyer_data["house_number"] != $buyer->house_number){
            $buyer->update_buyer_detail("house_number",$buyer_data["house_number"]);
        }
        if(!empty($buyer_data["street"]) && $buyer_data["street"] != $buyer->street){
            $buyer->update_buyer_detail("street",$buyer_data["street"]);
        }
        if(!empty($buyer_data["city"]) && $buyer_data["city"] != $buyer->city){
            $buyer->update_buyer_detail("city",$buyer_data["city"]);
        }
        if(!empty($buyer_data["state"]) && $buyer_data["state"] != $buyer->state){
            $buyer->update_buyer_detail("state",$buyer_data["state"]);
        }
        if(!empty($buyer_data["country"]) && $buyer_data["country"] != $buyer->country){
            $buyer->update_buyer_detail("country",$buyer_data["country"]);
        }
        if(!empty($buyer_data["postal_code"]) && $buyer_data["postal_code"] != $buyer->postal_code){
            $buyer->update_buyer_detail("postal_code",$buyer_data["postal_code"]);
        }
    }
    if($_POST["user_type"] == "seller"){
        $seller = new Seller($_POST["user_id"]);
        $seller_data = json_decode($_POST["seller_data"],true);
        if(!empty($seller_data["shopname"]) && $seller_data["shopname"] != $seller->shopname){
            $seller->update_seller_detail("shopname",$seller_data["shopname"]);
        }
        if(!empty($seller_data["phone_number"]) && $seller_data["phone_number"] != $seller->phone_number){
            $seller->update_seller_detail("phone_number",$seller_data["phone_number"]);
        }
        if(!empty($seller_data["house_number"]) && $seller_data["house_number"] != $seller->house_number){
            $seller->update_seller_detail("house_number",$seller_data["house_number"]);
        }
        if(!empty($seller_data["street"]) && $seller_data["street"] != $seller->street){
            $seller->update_seller_detail("street",$seller_data["street"]);
        }
        if(!empty($seller_data["city"]) && $seller_data["city"] != $seller->city){
            $seller->update_seller_detail("city",$seller_data["city"]);
        }
        if(!empty($seller_data["state"]) && $seller_data["state"] != $seller->state){
            $seller->update_seller_detail("state",$seller_data["state"]);
        }
        if(!empty($seller_data["country"]) && $seller_data["country"] != $seller->country){
            $seller->update_seller_detail("country",$seller_data["country"]);
        }
        if(!empty($seller_data["postal_code"]) && $seller_data["postal_code"] != $seller->postal_code){
            $seller->update_seller_detail("postal_code",$seller_data["postal_code"]);
        }
        if(!empty($seller_data["description"]) && $seller_data["description"] != $seller->description){
            $seller->update_seller_detail("description",$seller_data["description"]);
        }
        if(!empty($seller_data["directions"]) && $seller_data["directions"] != $seller->directions){
            $seller->update_seller_detail("directions",$seller_data["directions"]);
        }
    }
    if($_POST["user_type"] == "delivery"){
        $delivery = new delivery($_POST["user_id"]);
        $delivery_data = json_decode($_POST["delivery_data"],true);
        if(!empty($delivery_data["deliveryname"]) && $delivery_data["deliveryname"] != $delivery->deliveryname){
            $delivery->update_delivery_detail("deliveryname",$delivery_data["deliveryname"]);
        }
        if(!empty($delivery_data["phone_number"]) && $delivery_data["phone_number"] != $delivery->phone_number){
            $delivery->update_delivery_detail("phone_number",$delivery_data["phone_number"]);
        }
        if(!empty($delivery_data["house_number"]) && $delivery_data["house_number"] != $delivery->house_number){
            $delivery->update_delivery_detail("house_number",$delivery_data["house_number"]);
        }
        if(!empty($delivery_data["street"]) && $delivery_data["street"] != $delivery->street){
            $delivery->update_delivery_detail("street",$delivery_data["street"]);
        }
        if(!empty($delivery_data["city"]) && $delivery_data["city"] != $delivery->city){
            $delivery->update_delivery_detail("city",$delivery_data["city"]);
        }
        if(!empty($delivery_data["state"]) && $delivery_data["state"] != $delivery->state){
            $delivery->update_delivery_detail("state",$delivery_data["state"]);
        }
        if(!empty($delivery_data["country"]) && $delivery_data["country"] != $delivery->country){
            $delivery->update_delivery_detail("country",$delivery_data["country"]);
        }
        if(!empty($delivery_data["postal_code"]) && $delivery_data["postal_code"] != $delivery->postal_code){
            $delivery->update_delivery_detail("postal_code",$delivery_data["postal_code"]);
        }
        if(!empty($delivery_data["vehicle_type"]) && $delivery_data["vehicle_type"] != $delivery->vehicle_type){
            $delivery->update_delivery_detail("vehicle_type",$delivery_data["vehicle_type"]);
        }
        if(!empty($delivery_data["working_for_org"]) && $delivery_data["working_for_org"] != $delivery->working_for_org){
            $delivery->update_delivery_detail("working_for_org",$delivery_data["working_for_org"]);
        }
        if(!empty($delivery_data["org_name"]) && $delivery_data["org_name"] != $delivery->org_name){
            $delivery->update_delivery_detail("org_name",$delivery_data["org_name"]);
        }
    }
}

?>