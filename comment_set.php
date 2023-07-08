<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

require_once "comments.php";
require_once "seller.php";

use Markt\Comment;
use Markt\Seller;

if(isset($_POST["rating"]) && $_POST["rating"] > 0 && $_POST["comment_place"] == "Seller"){
    $seller = new Seller($_POST["comment_place_id"]);
    $seller->set_rating($_POST["rating"]);
}
if(isset($_POST) && !empty($_POST["comment_body"]) && !empty($_POST["comment_place_id"]) 
&& !empty($_POST["commenter"])){
    $comment = new Comment();
    $comment->comment_body = $_POST["comment_body"]; 
    $comment->comment_place = $_POST["comment_place"];
    $comment->comment_place_id = $_POST["comment_place_id"];
    $comment->commenter = $_POST["commenter"];
    $comment->comment_title = $_POST["comment_title"];
    if ($comment->create_comment()) {
        $new_comment = array();
        $new_comment["comment_body"] = $comment->comment_body;
        $new_comment["comment_place"] = $comment->comment_place;
        $new_comment["comment_place_id"] = $comment->comment_place_id;
        $new_comment["commenter"] = $comment->commenter;
        $new_comment["comment_title"] = $comment->comment_title;
        echo json_encode($new_comment);
    } else {
        echo json_encode(false);
    }
}
else{
    echo json_encode(false);
}
?>