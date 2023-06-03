<?php

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
header('Access-Control-Allow-Methods:  POST, PUT, GET');

$categories_json = file_get_contents("categories.json");
$categories = json_decode($categories_json,true);


if (isset($_GET)) {
    if($_GET["type"] == "main_names"){
        $all_main_names = [];
        foreach($categories as $category){
            $all_main_names[count($all_main_names)] = $category["name"];
        }
        echo json_encode($all_main_names);
    }
    elseif ($_GET["type"] == "all") {
        echo json_encode($categories);
    }
    else{
        echo json_encode($categories);
    }
} else {
    echo json_encode("request not in correct format");
}


?>