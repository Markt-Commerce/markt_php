<?php

namespace Markt\DB;

/**
 * Product Querying Database connections, retrieval, storage, modification of items in a buyer product query 
 */
class ProductQueryDB{

    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";

    private $conn;

    private $query_pq;

    /**
     * connects to the database
     * and creates an order table if it does not already exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if (!$this->conn) {
            
        }
        else{
            $this->query_pq = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS product_query (
                id INT NOT NULL AUTO_INCREMENT, query_id VARCHAR(400) NOT NULL , 
                buyer_id VARCHAR(400) NOT NULL , date_created DATETIME NOT NULL ,
                message VARCHAR(400) NOT NULL, category VARCHAR(400) NOT NULL,
                stale_time BIGINT NOT NULL
                PRIMARY KEY (`id`))");
        }
    }

    
    /**
     * Summary of create_order
     * @param mixed $Productquerydata
     * @return \mysqli_result|bool
     */
    public function create_order($Productquerydata){
        return mysqli_query($this->conn,"INSERT INTO product_query(
             query_id , buyer_id , date_created , message, category, stale_time 
            ) 
            VALUES (
            '{$Productquerydata["query_id"]}','{$Productquerydata["buyer_id"]}',
            '{$Productquerydata["date_created"]}',
            '{$Productquerydata["message"]}','{$Productquerydata["category"]}',
            '{$Productquerydata["stale_time"]}')");
    }


    /**
     * Summary of get_all_queries
     * @return array
     */
    public function get_all_queries(){
        $query = mysqli_query($this->conn,"SELECT * FROM product_query WHERE 1");
        return mysqli_fetch_all($query,MYSQLI_ASSOC);
    }

    /**
     * Summary of get_all_queries_relating_to_buyer
     * @param mixed $buyer_id
     * @return array
     */
    public function get_all_queries_relating_to_buyer($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $query = mysqli_query($this->conn,"SELECT * FROM product_query WHERE buyer_id = '{$buyer_id}'");
        return mysqli_fetch_all($query,MYSQLI_ASSOC);
    }

    /**
     * Summary of get_all_queries_based_on_category
     * @param mixed $categories
     * @return array
     */
    public function get_all_queries_based_on_category($categories){
        $contat_str = "SELECT * FROM product_query WHERE category ";
        for ($i=0; $i < count($categories)-1; $i++) { 
            $categories[$i] = mysqli_real_escape_string($this->conn,$categories[$i]);
            $contat_str = $contat_str."LIKE '%{$categories[$i]}%' OR ";
        }
        $contat_str = $contat_str."LIKE '%{$categories[count($categories)-1]}%'";
        $query = mysqli_query($this->conn,$contat_str);
        return mysqli_fetch_all($query,MYSQLI_ASSOC);
    }

    /**
     * Summary of get_query
     * @param mixed $query_id
     * @return array|bool|null
     */
    public function get_query($query_id){
        $query_id = mysqli_real_escape_string($this->conn,$query_id);
        $query = mysqli_query($this->conn,"SELECT * FROM product_query WHERE query_id = '{$query_id}'");
        return mysqli_fetch_assoc($query);
    }

    /**
     * Summary of delete_query
     * @param mixed $query_id
     * @return array|bool|null
     */
    public function delete_query($query_id){
        $query_id = mysqli_real_escape_string($this->conn,$query_id);
        $query = mysqli_query($this->conn,"DELETE FROM product_query WHERE query_id = '{$query_id}'");
        return mysqli_fetch_assoc($query);
    }

}

?>