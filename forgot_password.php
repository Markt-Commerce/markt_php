<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require 'PHPMailer-master\PHPMailer-master\src\Exception.php';
require 'PHPMailer-master\PHPMailer-master\src\PHPMailer.php';
require 'PHPMailer-master\PHPMailer-master\src\SMTP.php';
require_once 'seller.php';
require_once 'buyer.php';
require_once 'delivery.php';

use PHPMailer\PHPMailer\PHPMailer; 
use Markt\buyer;
use Markt\delivery;
use Markt\Seller;

/** 
 * TODO: 
 * 1. create markt temporary email (gmail), the main email would use the host provider domain name
*/

$randomcode = random_int(100000,999999);
$time_to_expire = 180;

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

if(isset($_POST["user_type"]) && isset($_POST["email"]) && filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){


    $in_database = false;

    if($_POST["user_type"] == "buyer"){
        $buyer = new buyer();
        $in_database = $buyer->already_in_database($_POST["email"]);
    }
    elseif($_POST["user_type"] == "seller"){
        $seller = new Seller();
        $in_database = $seller->already_in_database($_POST["email"]);
    }
    elseif($_POST["user_type"] == "delivery"){
        $delivery = new delivery();
        $in_database = $delivery->already_in_database($_POST["email"]);
    }

    if($in_database){
        for($i = 0;$i < count($all_forgot_password_users);$i++){
            if($all_forgot_password_users[$i]["email"] == $_POST["email"]){
                unset($all_forgot_password_users[$i]);
                $all_forgot_password_users = array_values($all_forgot_password_users);
                break;
            }
        }
        $new_password_retrieval_entry = array();
        $new_password_retrieval_entry["email"] = $_POST["email"];
        $new_password_retrieval_entry["retrieval_code"] = $randomcode;
        $new_password_retrieval_entry["expiry_time"] = time() + $time_to_expire;

        //work on this later with internet
        /* $mail_text = "<div>
            <h2>Hey there.</h2>
            <p>we heard you forgot your password. Let's try to get you back in. Here is your verification code.</p>
            <h1 style='display:flex;justify-content:center;align-items:center'>".$randomcode."</h1>
            <p>Enter this code into the website to get back into your account and continue your 
            transactions</p>
            <p>P.S: This code expires in three minutes.</p>
            <p>If you did not request for this code, please ignore this mail and change your
            password as some one might have gotten access to your password</p>
            <p>Do not reply this message as you will not receive any response.</p><div>";

        $mail_text_non_html = "
            Hey there.
            we heard you forgot your password. Let's try to get you back in. Here is your verification code.
            ".$randomcode."
            Enter this code into the website to get back into your account and continue your 
            transactions
            P.S: This code expires in three minutes.
            If you did not request for this code, please ignore this mail and change your
            password as some one might have gotten access to your password
            Do not reply this message as you will not receive any response.";

        $mail = new PHPMailer(true);                              
        $mail->isSMTP();                                     
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;                              
        $mail->Username = 'sender@gmail.com';             
        $mail->Password = 'password';                         
        $mail->SMTPSecure = 'tls';  
        $mail->Port = 587;

        $mail->From = 'from@example.com';
        $mail->FromName = 'Mailer';
        $mail->addAddress('joe@example.net', 'Joe User');    
        $mail->addAddress('ellen@example.com');               
        $mail->addReplyTo('info@example.com', 'Information');

        $mail->WordWrap = 50;                                 
        $mail->isHTML(true);                                  

        $mail->Subject = "Let's get you back in";
        $mail->Body    = $mail_text;
        $mail->AltBody = $mail_text_non_html;  */

        /* if($mail->send()){
            $all_forgot_password_users[count($all_forgot_password_users)] = $new_password_retrieval_entry;
            file_put_contents(
                                "dbconnections/password%forgotten%users%and%codes.json",
                                json_encode($all_forgot_password_users));
                                echo json_encode($randomcode);
        }
        else{
            echo json_encode("could not register code");
            echo json_encode($randomcode);
        } */
        $all_forgot_password_users[count($all_forgot_password_users)] = $new_password_retrieval_entry;
            file_put_contents(
                                "dbconnections/password%forgotten%users%and%codes.json",
                                json_encode($all_forgot_password_users));
                                echo json_encode($randomcode);
    }
    else{
        if($_POST["user_type"] == "buyer"){
            echo json_encode("user not in our buyer database");
        }
        elseif($_POST["user_type"] == "seller"){
            echo json_encode("user not in our seller database");
        }
        elseif($_POST["user_type"] == "delivery"){
            echo json_encode("user not in our delivery database");
        }
    }
}
else{
    echo json_encode("email or type not provided or email is not in correct format");
}

?>