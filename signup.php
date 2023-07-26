<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

include_once "buyer.php";
require_once "seller.php";
require_once "delivery.php";

use Markt\buyer;
use Markt\delivery;
use Markt\Seller;

$buyer = new buyer();
$delivery = new delivery();
$seller = new Seller();

if(isset($_POST)){
    if($_POST["user_type"] == "buyer" || !empty($_POST["username"])){
        if(!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["phone_number"])){
            $buyer->username = $_POST["username"];
            array_push($buyer->buyer_payment_data_array,json_decode($_POST["payment_details"],true));
            $buyer->city = $_POST["city"];
            $buyer->street = $_POST["street"];
            $buyer->country = $_POST["country"];
            $buyer->email = $_POST["email"];
            $buyer->house_number = $_POST["house_number"];
            $buyer->latitude = $_POST["latitude"];
            $buyer->longtitude = $_POST["longtitude"];
            $buyer->password = $_POST["password"];
            $buyer->postal_code = $_POST["postal_code"];
            $buyer->phone_number = $_POST["phone_number"];
            $buyer->set_buyer_profile_image($_FILES["profile_image"]);
            $buyer->state = $_POST["state"];
            if($buyer->create_new_buyer()){
                session_start();
                $_SESSION["user_id"] = $buyer->get_buyer_id();
                $_SESSION["user_type"] = "buyer";
                setcookie("user_id",$buyer->get_buyer_id(),time()+60*60*2,"","",null,true);
                setcookie("user_type","buyer",time()+60*60*2,"/","",null,true);
                $result_data = array();
                $result_data["saved"] = true;
                $result_data["user_id"] = $buyer->get_buyer_id();
                $result_data["username"] = $buyer->username;
                $result_data["user_type"] = "buyer";
                $result_data["profile_image"] = (empty($buyer->profile_image)) ? "placeholder-image" : $buyer->profile_image;
                echo json_encode($result_data); 
            }
            else{
                echo json_encode("cannot create new buyer");
            }
        }
        else{
            echo json_encode("email,phone_no or password not provided");
        }
    }
    elseif($_POST["user_type"] == "seller" || !empty($_POST["shopname"])){
        if(!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["phone_number"])){
            array_push($seller->seller_payment_data_array,json_decode($_POST["payment_details"],true));
            $seller->category = $_POST["category"];
            $seller->city = $_POST["city"];
            $seller->country = $_POST["country"];
            $seller->description = $_POST["description"];
            $seller->directions = $_POST["directions"];
            $seller->email = $_POST["email"];
            $seller->house_number = $_POST["house_number"]; 
            $seller->latitude = $_POST["latitude"];
            $seller->longtitude = $_POST["longtitude"];
            $seller->password = $_POST["password"];
            $seller->phone_number = $_POST["phone_number"];
            $seller->postal_code = $_POST["postal_code"];
            $seller->set_seller_profile_image($_FILES["profile_image"]); 
            $seller->shopname = $_POST["shopname"];
            $seller->state = $_POST["state"];
            $seller->street = $_POST["street"];
            if($seller->create_new_seller()){
                session_start();
                $_SESSION["user_id"] = $seller->get_seller_id();
                $_SESSION["user_type"] = "seller";
                setcookie("user_id",$seller->get_seller_id(),time()+60*60*2,"","",null,true);
                setcookie("user_type","seller",time()+60*60*2,"/","",null,true);
                $result_data = array();
                $result_data["saved"] = true;
                $result_data["user_id"] = $seller->get_seller_id();
                $result_data["username"] = $seller->shopname;
                $result_data["user_type"] = "seller";
                $result_data["profile_image"] = (empty($seller->profile_image)) ? "profile-placeholder-image" : $seller->profile_image;
                echo json_encode($result_data);
            }
            else{
                echo json_encode("cannot create new seller");
            }
        }
        else{
            echo json_encode("email,phone_no or password not provided");
        }
    }
    elseif($_POST["user_type"] == "delivery" || !empty($_POST["deliveryname"])){
        if(!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["phone_number"])){
            array_push($delivery->delivery_payment_data_array,json_decode($_POST["payment_details"],true));
            $delivery->city = $_POST["city"];
            $delivery->country = $_POST["country"];
            $delivery->deliveryname = $_POST["deliveryname"];
            $delivery->email = $_POST["email"];
            $delivery->house_number = $_POST["house_number"];
            $delivery->latitude = $_POST["latitude"];
            $delivery->longtitude = $_POST["longtitude"];
            $delivery->org_name = $_POST["org_name"];
            $delivery->password = $_POST["password"];
            $delivery->phone_number = $_POST["phone_number"];
            $delivery->postal_code = $_POST["postal_code"];
            $delivery->set_delivery_profile_image($_FILES["profile_image"]);
            $delivery->state = $_POST["state"];
            $delivery->street = $_POST["street"];
            $delivery->vehicle_type = $_POST["vehicle_type"];
            $delivery->working_for_org = $_POST["working_for_org"];
            if($delivery->create_new_delivery()){
                session_start();
                $_SESSION["user_id"] = $delivery->get_delivery_id();
                $_SESSION["user_type"] = "delivery";
                setcookie("user_id",$delivery->get_delivery_id(),time()+60*60*2,"","",null,true);
                setcookie("user_type","delivery",time()+60*60*2,"/","",null,true);
                $result_data = array();
                $result_data["saved"] = true;
                $result_data["user_id"] = $delivery->get_delivery_id();
                $result_data["username"] = $delivery->deliveryname;
                $result_data["user_type"] = "delivery";
                $result_data["profile_image"] = (empty($delivery->profile_image)) ? "placeholder-image" : $delivery->profile_image;
                echo json_encode($result_data); 
            }
            else{
                echo json_encode("cannot create new delivery");
            }
        }
        else{
            echo json_encode("email,phone_no or password not provided");
        }
    }
    else{
        echo json_encode("user type not provided");
    }

}else{
    echo json_encode("not set POST");
}

?>