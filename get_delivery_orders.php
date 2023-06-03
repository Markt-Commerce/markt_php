<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "delivery.php";

use Markt\delivery;

/**
 * Gets the delivery orders. The delivery person has to set if he wants to use his present location and
 * the effects of not doing so and if he dos not want to use his present location. If the delivery sets
 * the location, the orders would be gotten from the database using the new location that was sent. If
 * the delivery does not set the location, the last location saved in the database would be used 
 * instead. Although it is very important that delivery location is set.
 */

if(isset($_GET) && !empty($_COOKIE["user_id"]) && !empty($_COOKIE["user_type"])){
    if($_COOKIE["user_type"] == "delivery"){
        if(!empty($_GET["longtitude"]) && !empty($_GET["latitude"])){
            $delivery = new delivery($_COOKIE["user_id"]);
            $delivery->longtitude = $_GET["longtitude"];
            $delivery->latitude = $_GET["latitude"];
            echo json_encode($delivery->get_delivery_orders());
        }
        else{
            $delivery = new delivery($_COOKIE["user_id"]);
            echo json_encode($delivery->get_delivery_orders());
        }
    }
    else{
        echo json_encode("wrong user type");
    }
}