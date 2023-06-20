<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once 'buyer.php';

use Markt\buyer;

//remember to change $_GET back to $_COOKIE

if (isset($_GET) && !empty($_GET["user_id"]) && !empty($_GET["cart_id"])) {
    $buyer = new buyer($_GET["user_id"]);
    echo json_encode($buyer->remove_from_cart($_GET["cart_id"]));
} else {
    echo json_encode("some parameters are not set");
} 

?>