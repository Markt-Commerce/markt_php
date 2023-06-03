<?php

namespace Markt\DB;

/**
 * Class for adding, removing and updating buyer favorites in the database
 */
class FavoritesDB{

    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;
    private $query_favorite;

    /**
     * instantiates the class and creates a new favorites table if it does not exist
     */
    public function __construct(){
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){
            
        }
        else{
            $this->query_favorite = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS favorites (
                id INT NOT NULL AUTO_INCREMENT , unique_id VARCHAR(400) NOT NULL , 
                buyer_id VARCHAR(400) NOT NULL , favorite_type VARCHAR(255) NOT NULL , 
                favorite_id VARCHAR(400) NOT NULL , PRIMARY KEY (`id`))");
        }
    }

    /**
     * adds a favorite item to the database i.e creates a new favorite in the database.
     * @param array $Favorite An associative array containing the details about the favorite.
     * Format of the associative array:
     * $Favorite["unique_id"] =  unique id of the favorite row to uniquely identify it
     * $Favorite["buyer_id"] = the id of the buyer
     * $Favorite["favorite_type"] = the type of favorite (can only be of two types `product` and `seller`)
     * $Favorite["favorite_id"] = the id of the favorite (either `product_id` or `seller_id`)
     * @return bool
     */
    public function add_favorite($Favorite){
        $Favorite["unique_id"] = mysqli_real_escape_string($this->conn,$Favorite["unique_id"]);
        $Favorite["buyer_id"] = mysqli_real_escape_string($this->conn,$Favorite["buyer_id"]);
        $Favorite["favorite_type"] = mysqli_real_escape_string($this->conn,$Favorite["favorite_type"]);
        $Favorite["favorite_id"] = mysqli_real_escape_string($this->conn,$Favorite["favorite_id"]);
        return mysqli_query($this->conn,"INSERT INTO favorites(
            unique_id, buyer_id, favorite_type, favorite_id) VALUES (
                '{$Favorite["unique_id"]}','{$Favorite["buyer_id"]}',
                '{$Favorite["favorite_type"]}','{$Favorite["favorite_id"]}')");
    }

    /**
     * gets all available favorites in the database
     * @return array
     */
    public function get_all_favorites(){
        $query = mysqli_query($this->conn,"SELECT * FROM favorites WHERE 1");
        $result = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $result;
    }

    /**
     * gets the total number of people who added the product or seller to their favorites
     * @param string $product_id
     * @return int|string
     */
    public function get_total_favorite_product_number($product_id){
        $product_id = mysqli_real_escape_string($this->conn,$product_id);
        $query = mysqli_query($this->conn,"SELECT * FROM favorites WHERE favorite_id = '{$product_id}'");
        $favorite_amount = mysqli_num_rows($query);
        return $favorite_amount;
    }

    /**
     * checks if a favorite of a buyer has already been saved or is already in the database
     * @param string $buyer_id
     * @param string $favorite_type
     * @param string $favorite_id
     * @return bool
     */
    public function favorite_exists($buyer_id,$favorite_type,$favorite_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $favorite_type = mysqli_real_escape_string($this->conn,$favorite_type);
        $favorite_id = mysqli_real_escape_string($this->conn,$favorite_id);
        $query = mysqli_query($this->conn,"SELECT * FROM favorites WHERE 
                                                            buyer_id = '{$buyer_id}'
                                                             AND favorite_id = '{$favorite_id}' 
                                                             AND favorite_type = '{$favorite_type}'");
        $result_size = mysqli_num_rows($query);
        return $result_size > 0;
    }

    /**
     * gets the favorites of a specific buyer using his id
     * @param string $buyer_id
     * @return array NOTE the result array returned does not contain the `product` or `seller` itself
     * but their respective id's. The product or seller would have to be gotten by calling its 
     * respective classes
     */
    public function get_buyer_favorites($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $query = mysqli_query($this->conn,"SELECT * FROM favorites WHERE buyer_id = '{$buyer_id}'");
        $result = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $result;
    }


    /**
     * removes a favorite from the database
     * @param string $favorite_id
     * @return \mysqli_result|bool
     */
    public function remove_favorite($favorite_id){
        $favorite_id = mysqli_real_escape_string($this->conn,$favorite_id);
        return mysqli_query($this->conn,"DELETE FROM favorites WHERE favorite_id = '{$favorite_id}'");
    }

    /**
     * removes all favorites of a buyer from the database
     * @param string $buyer_id
     * @return \mysqli_result|bool
     */
    public function remove_all_buyer_favorites($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        return mysqli_query($this->conn,"DELETE FROM favorites WHERE buyer_id = '{$buyer_id}'");
    }

}

?>