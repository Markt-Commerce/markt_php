<?php

namespace Markt\DB;

/**
 * class for creating, deleting and updating cart items in a database
 */
class CartDB{

    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;
    private $query_cart;

    /**
     * instantiates the class and creates a new cart table if it does not already exist
     */
    function __construct(){
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){
            
        }
        else{
            $this->query_cart = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS cart (
                id INT NOT NULL AUTO_INCREMENT , cart_id VARCHAR(400) NOT NULL , 
                buyer_id VARCHAR(400) NOT NULL , product_id VARCHAR(400) NOT NULL , 
                quantity INT NOT NULL , has_discount BOOLEAN NOT NULL , 
                discount_price FLOAT NOT NULL , discount_percent FLOAT NOT NULL , 
                PRIMARY KEY (`id`))");
        }
    }

    /**
     * creates a new cart item
     * @param mixed $Cart the array containing the cart items
     * format:
     * $Cart["cart_id"]
     * $Cart["buyer_id"]
     * $Cart["product_id"]
     * $Cart["quantity"]
     * this format should be followed to avoid errors
     * @return \mysqli_result|bool
     */
    public function create_cart_item($Cart){
        return mysqli_query($this->conn,"INSERT INTO cart(
            cart_id, buyer_id, product_id, quantity, has_discount, 
                discount_price, discount_percent) VALUES (
                '{$Cart["cart_id"]}','{$Cart["buyer_id"]}',
                '{$Cart["product_id"]}','{$Cart["quantity"]}',
                '{$Cart["has_discount"]}','{$Cart["discount_price"]}',
                '{$Cart["discount_percent"]}')");
    }

    /**
     * get the items in the cart of a buyer using the buyer's id
     * @param string $buyer_id
     * @return array
     */
    public function get_buyer_cart_items($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $query = mysqli_query($this->conn,"SELECT * FROM cart WHERE buyer_id = '{$buyer_id}'");
        return mysqli_fetch_all($query,MYSQLI_ASSOC);
    }

    /**
     * get the number of items in the cart of a buyer using the buyer's id
     * @param string $buyer_id
     * @return int
     */
    public function get_number_of_buyer_cart_items($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $query = mysqli_query($this->conn,"SELECT * FROM cart WHERE buyer_id = '{$buyer_id}'");
        return mysqli_num_rows($query);
    }

    /**
     * gets a cart item through its product id
     * @param string $buyer_id
     * @param string $product_id
     * @return int|string
     */
    public function get_buyer_cart_item_through_product_id($buyer_id,$product_id){
        $query = mysqli_query($this->conn,"SELECT * FROM cart WHERE 
                                                    buyer_id = '{$buyer_id}' AND 
                                                    product_id = '{$product_id}'");
        $result = mysqli_num_rows($query);
        return $result;
    }

    /**
     * checks if a cart item already exists in the database
     * @param string $buyer_id
     * @param string $product_id
     * @return bool
     */
    public function item_already_exists($buyer_id,$product_id){
        $query = mysqli_query($this->conn,"SELECT * FROM cart WHERE 
                                                    buyer_id = '{$buyer_id}' AND 
                                                    product_id = '{$product_id}'");
        $result = mysqli_num_rows($query);
        return $result > 0;
    }

    public function update_quantity($product_id,$value){
        $product_id = mysqli_real_escape_string($this->conn,$product_id);
        return mysqli_query($this->conn,"UPDATE cart SET quantity = {$value}
                                                            WHERE product_id = '{$product_id}'");
    }

    /**
     * removes an item from the buyer's cart
     * @param mixed $item_id
     * @return \mysqli_result|bool
     */
    public function remove_buyer_cart_item($item_id){
        $item_id = mysqli_real_escape_string($this->conn,$item_id);
        return mysqli_query($this->conn,"DELETE FROM cart WHERE cart_id = '{$item_id}'");
    }

}

?>