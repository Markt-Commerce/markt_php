<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'seller.php';
require_once 'buyer.php';
require_once 'delivery.php';

use Markt\buyer;
use Markt\delivery;
use Markt\Seller;

if(isset($_POST) && !empty($_POST["user_type"])){
    $user_login = array();
    $result = array();
    if($_POST["user_type"] == "buyer"){
        $user_login["usernameoremailorphonenumber"] = $_POST["usernameoremailorphonenumber"];
        $user_login["password"] = $_POST["password"];
        $buyer = new buyer($user_login);
        if($buyer->loggedin){
            $_SESSION["user_id"] = $buyer->get_buyer_id();
            $_SESSION["user_type"] = "buyer";
            $result["user"] = $buyer->username;
            $result["user_id"] = $buyer->get_buyer_id();
            $result["message"] = "ok";
            $result["profile_image"] = (empty($buyer->profile_image)) ? "profile-placeholder-image" : $buyer->profile_image;
            setcookie("user_id",$buyer->get_buyer_id(),time()+60*60*2,"","",null,true);
            setcookie("user_type","buyer",time()+60*60*2,"/","",null,true);
            session_start();
            echo json_encode($result);
        }
        else{ 
            $result["message"] = "bad credentials";
            echo json_encode($result);
        }
    }
    elseif($_POST["user_type"] == "seller"){
        $user_login["shopnameoremailorphonenumber"] = $_POST["usernameoremailorphonenumber"];
        $user_login["password"] = $_POST["password"];
        $seller = new Seller($user_login);
        if($seller->loggedin){
            $_SESSION["user_id"] = $seller->get_seller_id();
            $_SESSION["user_type"] = "seller";
            $result["user"] = $seller->shopname;
            $result["user_id"] = $seller->get_seller_id();
            $result["message"] = "ok";
            $result["profile_image"] = (empty($seller->profile_image)) ? "profile-placeholder-image" : $seller->profile_image;
            setcookie("user_id",$seller->get_seller_id(),time()+60*60*2,"","",null,true);
            setcookie("user_type","seller",time()+60*60*2,"/","",null,true);
            session_start();
            echo json_encode($result);
        }
        else{
            $result["message"] = "bad credentials";
            echo json_encode($result);
        }
    }
    elseif($_POST["user_type"] == "delivery"){
        $user_login["deliverynameoremailorphonenumber"] = $_POST["usernameoremailorphonenumber"];
        $user_login["password"] = $_POST["password"];
        $delivery = new delivery($user_login);
        if($delivery->loggedin){
            $_SESSION["user_id"] = $delivery->get_delivery_id();
            $_SESSION["user_type"] = "delivery";
            $result["user"] = $delivery->deliveryname;
            $result["user_id"] = $delivery->get_delivery_id();
            $result["message"] = "ok";
            $result["profile_image"] = (empty($delivery->profile_image)) ? "profile-placeholder-image" : $delivery->profile_image;
            setcookie("user_id",$delivery->get_delivery_id(),time()+60*60*2,"","",null,true);
            setcookie("user_type","delivery",time()+60*60*2,"/","",null,true);
            session_start();
            echo json_encode($result);
        }
        else{
            $result["message"] = "bad credentials";
            echo json_encode($result);
        }
    } 
}

?>