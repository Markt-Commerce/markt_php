<?php

namespace Markt\DB;


/**
 * Class for the retrieval, creation, distribution and deletion of products in the database
 */
class ProductDB{

    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;

    private $query_product;

    /**
     * connects with the database and creates a new product table if it does not already exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){

        }
        else{
            $this->query_product = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS products (
                id INT NOT NULL AUTO_INCREMENT, product_id VARCHAR(400) NOT NULL , product_name VARCHAR(400) NOT NULL , 
                product_type VARCHAR(255) NOT NULL , product_price FLOAT NOT NULL , 
                product_description VARCHAR(400) NOT NULL , product_category VARCHAR(400) NOT NULL,
                tags VARCHAR(400) NOT NULL, product_quantity INT NOT NULL , 
                estimated_size INT NULL, seller_id VARCHAR(400) NOT NULL , 
                desc_under VARCHAR(400) NOT NULL DEFAULT 'NONE' , 
                PRIMARY KEY (id))");
        }
    }

    /**
     * creates a new product in the database
     * @param array $ProductData an associative array of all the product data
     * @return boolean
     */
    public function create_product($ProductData){
        return mysqli_query($this->conn,"INSERT INTO products(
            product_id, product_name, product_type, product_price, 
            product_description, product_category, tags, product_quantity, 
            estimated_size,seller_id, desc_under) 
            VALUES ('{$ProductData["product_id"]}','{$ProductData["product_name"]}',
            '{$ProductData["product_type"]}','{$ProductData["product_price"]}',
            '{$ProductData["product_description"]}','{$ProductData["product_category"]}',
            '{$ProductData["tags"]}','{$ProductData["product_quantity"]}',
            '{$ProductData["estimated_size"]}','{$ProductData["seller_id"]}','{$ProductData["desc_under"]}')");
    }

    /**
     * 
     */
    public function get_all_products(){
        $products_query = mysqli_query($this->conn,"SELECT * FROM products WHERE 1");
        return mysqli_fetch_all($products_query,MYSQLI_ASSOC);
    }

    /**
     * gets random products specified by in specified packets. The number of products to 
     * produce depends on `$packet_number`
     * @param int $packet_number
     * @return array
     */
    public function get_products_in_randomized_pack($packet_number){
        $product_query = mysqli_query($this->conn, "SELECT * FROM products WHERE product_quantity > 0");
        $all_products = mysqli_fetch_all($product_query,MYSQLI_ASSOC);
        $number_of_data = mysqli_num_rows($product_query);
        $randomized_products = array();
        if($number_of_data <= $packet_number){
            return $all_products;
        }
        else{
            while($packet_number != 0){
                $replicas = 0;
                $some_random_index = random_int(0,count($all_products)-1);
                if (in_array($some_random_index,$randomized_products)) {
                    $replicas = $replicas + 1;
                }
                if($replicas == 0){
                    $randomized_products[count($randomized_products)] = $some_random_index;
                    $packet_number = $packet_number - 1;
                }
            }
            for($i = 0;$i < count($randomized_products);$i++){
                $randomized_products[$i] = $all_products[$randomized_products[$i]];
            }
            return $randomized_products;
        }
    }

    /**
     * gets a product from the database using its id
     * @param int $product_id
     * @return mixed
     */
    public function get_product($product_id){
        $product_id = mysqli_real_escape_string($this->conn,$product_id);
        $product_query = mysqli_query($this->conn,"SELECT * FROM products 
                                        WHERE product_id = '{$product_id}'");
        $product = mysqli_fetch_assoc($product_query);
        return $product;
    }

    /**
     * gets a product from the database using its seller_id
     * @param int $seller_id
     * @return mixed
     */
    public function get_products_with_seller_id($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $product_query = mysqli_query($this->conn,"SELECT * FROM products 
                                        WHERE seller_id = '{$seller_id}'");
        $product = mysqli_fetch_all($product_query,MYSQLI_ASSOC);
        return $product;
    }

    /**
     * gets a product that falls under a certain category 
     * @param string $category
     * @return array
     */
    public function get_product_using_category($category){
        $category = mysqli_real_escape_string($this->conn,$category);
        $product_query = mysqli_query($this->conn,"SELECT * FROM products 
                                        WHERE product_category LIKE '%{$category}%' AND product_quantity > 0");
        $result_products = mysqli_fetch_all($product_query,MYSQLI_ASSOC);
        return $result_products;
    }

    public function get_random_packets_of_product_using_category($category,$packet_number){
        $product_query = mysqli_query($this->conn, "SELECT * FROM products 
                                                            WHERE product_category LIKE '%{$category}%' AND product_quantity > 0");
        $all_products = mysqli_fetch_all($product_query,MYSQLI_ASSOC);
        $number_of_data = mysqli_num_rows($product_query);
        $randomized_products = array();
        if($number_of_data <= $packet_number){
            return $all_products;
        }
        else{
            while($packet_number != 0){
                $replicas = 0;
                $some_random_index = random_int(0,count($all_products)-1);
                if (in_array($some_random_index,$randomized_products)) {
                    $replicas = $replicas + 1;
                }
                if($replicas == 0){
                    $randomized_products[count($randomized_products)] = $some_random_index;
                    $packet_number = $packet_number - 1;
                }
            }
            for($i = 0;$i < count($randomized_products);$i++){
                $randomized_products[$i] = $all_products[$randomized_products[$i]];
            }
            return $randomized_products;
        }
    }

    /**
     * gets a product having the same value as `$product_name`
     * @param string $product_name
     * @return mixed
     */
    public function get_products_with_name($product_name){
        $product_name = mysqli_real_escape_string($this->conn,$product_name);
        $product_query = mysqli_query($this->conn,"SELECT * FROM products 
                                        WHERE product_name LIKE '%{$product_name}%' AND product_quantity > 0");
        $product = mysqli_fetch_all($product_query,MYSQLI_ASSOC);
        return $product;
    }

    /**
     * get products with a specified name or category
     * @param string $product_name
     * @param string $category
     * @return array
     */
    public function get_products_with_specified_name_and_category($product_name,$category){
        $product_name = mysqli_real_escape_string($this->conn,$product_name);
        $category = mysqli_real_escape_string($this->conn,$category);
        $product_query = mysqli_query($this->conn,"SELECT * FROM products 
                                        WHERE product_name LIKE '%{$product_name}%'
                                        AND product_category LIKE '%{$category}%' AND product_quantity > 0");
        $product = mysqli_fetch_all($product_query,MYSQLI_ASSOC);
        return $product;
    }

    /**
     * get the number of products with a specified name or category
     * @param string $product_name
     * @param string $category
     * @return int|string
     */
    public function get_amount_of_searched_products($product_name,$category){
        $product_name = mysqli_real_escape_string($this->conn,$product_name);
        $category = mysqli_real_escape_string($this->conn,$category);
        $product_query = mysqli_query($this->conn,"SELECT * FROM products 
                                        WHERE product_name LIKE '%{$product_name}%'
                                        AND product_category LIKE '%{$category}%' AND product_quantity > 0");
        return mysqli_num_rows($product_query);
    }

    /**
     * gets products that have their sizes equal to the size specified
     * @param integer $size
     * @return array
     */
    public function get_products_with_particular_size_range($size){
        $size = mysqli_real_escape_string($this->conn,$size);
        $query = mysqli_query($this->conn,"SELECT * FROM products WHERE estimated_size <= {$size}");
        $result = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $result;
    }

    /**
     * gets the seller id of a product
     * @param mixed $product_id
     * @return mixed
     */
    public function get_product_seller($product_id){
        $product_id = mysqli_real_escape_string($this->conn,$product_id);
        $query = mysqli_query($this->conn,"SELECT * FROM products WHERE product_id = {$product_id}");
        $result = mysqli_fetch_assoc($query);
        return $result["seller_id"];
    }

    /** 
     * update part of a product with any value available
     * @param int $product_id the id of the product
     * @param string $column_to_be_updated
     * @param mixed $value
     * @return boolean
    */
    public function update_product($product_id,$column_to_be_updated,$value){
        if(!is_string($value)){
            return mysqli_query($this->conn,"UPDATE products 
                                        SET {$column_to_be_updated} = {$value}
                                        WHERE product_id = '{$product_id}'");
        }
        $value = mysqli_real_escape_string($this->conn,$value);
        return mysqli_query($this->conn,"UPDATE products 
                                        SET {$column_to_be_updated} = '{$value}'
                                        WHERE product_id = '{$product_id}'");
    }

    /** 
     * update multiple parts of a product with any of the values and columns provided
     * @param string $product_id the id of the product
     * @param array $column_to_be_updated
     * @param array $value
     * @return boolean
    */
    public function update_multiple_product_values($product_id,$columns_to_be_updated,$values){
        $conn_str = "SET";
        $column_amount = count($columns_to_be_updated);
        $value_amount = count($values);
        for ($i=0; $i < $column_amount-1; $i++) { 
            if (!is_string($values[$i])) {
                $conn_str = $conn_str." {$columns_to_be_updated[$i]} = {$values[$i]},";
            }
            else{
                $conn_str = $conn_str." {$columns_to_be_updated[$i]} = '{$values[$i]}',";
            }
        }
        if (!is_string($values[$value_amount-1])) {
            $conn_str = $conn_str." {$columns_to_be_updated[$column_amount-1]} = {$values[$value_amount-1]}";
        }
        else{
            $conn_str = $conn_str." {$columns_to_be_updated[$column_amount-1]} = '{$values[$value_amount-1]}'";
        }
        return mysqli_query($this->conn,"UPDATE products ".$conn_str."
                                        WHERE product_id = '{$product_id}'");
    }

    /** 
     * delete a product using its id
     * @param int $product_id the id of the product
     * @return boolean
    */
    public function delete_product($product_id){
        $product_id = mysqli_real_escape_string($this->conn,$product_id);
        return mysqli_query($this->conn,"DELETE FROM products WHERE product_id = '{$product_id}'");
    }
}

?>