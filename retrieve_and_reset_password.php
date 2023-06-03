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

$all_forgot_password_users_json = file_get_contents("dbconnections/password%forgotten%users%and%codes.json");

function remove_expired_codes(){
    global $all_forgot_password_users_json;
    $all_forgot_password_users = json_decode($all_forgot_password_users_json,true);

    for($i = 0;$i > count($all_forgot_password_users);$i++){
        if($all_forgot_password_users[$i]["expiry_time"] < time()){
            unset($all_forgot_password_users[$i]);
        }
    }
    $all_forgot_password_users = array_values($all_forgot_password_users);
    return $all_forgot_password_users;
}

$all_forgot_password_users = remove_expired_codes();

if(isset($_POST["user_type"]) && isset($_POST["email"]) 
        && isset($_POST["password"]) && isset($_POST["retrieval_code"])){
    if($_POST["user_type"] == "buyer"){
        for($i = 0;$i < count($all_forgot_password_users);$i++){
            if($all_forgot_password_users[$i]["email"] == $_POST["email"]
                && $all_forgot_password_users[$i]["retrieval_code"] == $_POST["retrieval_code"]){
                    $buyer = new buyer();
                    $password_set = $buyer->change_password($_POST["email"],$_POST["password"]);
                    if($password_set){
                        unset($all_forgot_password_users[$i]);
                        $all_forgot_password_users = array_values($all_forgot_password_users);
                        file_put_contents("dbconnections/password%forgotten%users%and%codes.json",
                                                json_encode($all_forgot_password_users));
                        echo json_encode($password_set);
                        break;
                    }
                }
        }
    }
    elseif($_POST["user_type"] = "seller"){
        for($i = 0;$i < count($all_forgot_password_users);$i++){
            if($all_forgot_password_users[$i]["email"] == $_POST["email"]
                && $all_forgot_password_users[$i]["retrieval_code"] == $_POST["retrieval_code"]){
                    $seller = new seller();
                    $password_set = $seller->change_password($_POST["email"],$_POST["password"]);
                    if($password_set){
                        unset($all_forgot_password_users[$i]);
                        $all_forgot_password_users = array_values($all_forgot_password_users);
                        file_put_contents("dbconnections/password%forgotten%users%and%codes.json",
                                                json_encode($all_forgot_password_users));
                        echo json_encode($password_set);
                        break;
                    }
                }
        }
    }
    elseif($_POST["user_type"] == "delivery"){
        for($i = 0;$i < count($all_forgot_password_users);$i++){
            if($all_forgot_password_users[$i]["email"] == $_POST["email"]
                && $all_forgot_password_users[$i]["retrieval_code"] == $_POST["retrieval_code"]){
                    $delivery = new delivery();
                    $password_set = $delivery->change_password($_POST["email"],$_POST["password"]);
                    if($password_set){
                        unset($all_forgot_password_users[$i]);
                        $all_forgot_password_users = array_values($all_forgot_password_users);
                        file_put_contents("dbconnections/password%forgotten%users%and%codes.json",
                                                json_encode($all_forgot_password_users));
                        echo json_encode($password_set);
                        break;
                    }
                }
        }
    }
    else{
        echo json_encode(false);
    }
}
else{
    echo json_encode(false);
}

?>