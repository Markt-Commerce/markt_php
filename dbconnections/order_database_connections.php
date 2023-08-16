<?php

namespace Markt\DB;

/**
 * Order Database connections, retrieval, storage, modification of items in a buyer order 
 */
class orderDB{

    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";

    private $conn;

    private $query_order;

    /**
     * connects to the database
     * and creates an order table if it does not already exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if (!$this->conn) {
            
        }
        else{
            $this->query_order = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS orders (
                id INT NOT NULL AUTO_INCREMENT, order_id VARCHAR(400) NOT NULL , 
                seller_id VARCHAR(400) NOT NULL , buyer_id VARCHAR(400) NOT NULL , 
                delivery_id VARCHAR(400) NOT NULL , product_id INT NOT NULL , 
                product_quantity INT NOT NULL , received_by_delivery BOOLEAN NOT NULL , 
                order_date DATE NOT NULL, receive_code VARCHAR(400) NOT NULL , 
                delivered BOOLEAN NOT NULL , delivery_code VARCHAR(400) NOT NULL , 
                accepted BOOLEAN NOT NULL , has_discount BOOLEAN NOT NULL , 
                discount_price FLOAT NOT NULL , discount_percent FLOAT NOT NULL , PRIMARY KEY (id))");
        }
    }

    /** 
     * creates a new order and fills it with info
     * @param array $Orderdata
     * @return boolean
    */
    public function create_order($Orderdata){
        return mysqli_query($this->conn,"INSERT INTO orders(
            order_id, seller_id, buyer_id, delivery_id, 
            product_id, product_quantity, received_by_delivery, order_date, receive_code, 
            delivered, delivery_code, accepted, has_discount, 
                discount_price, discount_percent) 
            VALUES (
            '{$Orderdata["order_id"]}','{$Orderdata["seller_id"]}','{$Orderdata["buyer_id"]}',
            '{$Orderdata["delivery_id"]}','{$Orderdata["product_id"]}','{$Orderdata["product_quantity"]}',
            '{$Orderdata["received_by_delivery"]}','{$Orderdata["order_date"]}',
            '{$Orderdata["receive_code"]}','{$Orderdata["delivered"]}',
            '{$Orderdata["delivery_code"]}','{$Orderdata["accepted"]}',
                '{$Orderdata["has_discount"]}','{$Orderdata["discount_price"]}',
                '{$Orderdata["discount_percent"]}')");
    }

    /**
     * Section for getting orders from the database
     */

    /** 
     * Gets orders connected to a particular seller using the 
     * `seller_id`
     * @param string $seller_id
     * @return array
    */
    public function get_orders_through_seller($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE seller_id = '{$seller_id}'");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /** 
     * Gets number of orders connected to a particular seller using the 
     * `seller_id`
     * @param string $seller_id
     * @return int
    */
    public function get_seller_order_amount($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE seller_id = '{$seller_id}'");
        return mysqli_num_rows($order_query);
    }

    /** 
     * Gets orders connected to a particular seller using the 
     * `seller_id`
     * @param string $seller_id
     * @return array
    */
    public function get_accepted_orders_through_seller($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE seller_id = '{$seller_id}'
                                                        AND accepted = 1");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /** 
     * Gets orders connected to a particular seller using the 
     * `seller_id`
     * @param string $seller_id
     * @return array
    */
    public function get_non_accepted_orders_through_seller($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE seller_id = '{$seller_id}'
                                                        AND accepted = 0");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /** 
     * Gets orders connected to a particular buyer using the 
     * `buyer_id`
     * @param string $buyer_id
     * @return array
    */
    public function get_orders_through_buyer($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE buyer_id = '{$buyer_id}'");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /** 
     * Gets the number of orders connected to a particular buyer using the 
     * `buyer_id`
     * @param string $buyer_id
     * @return int
    */
    public function get_buyer_order_amount($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE buyer_id = '{$buyer_id}'");
        return mysqli_num_rows($order_query);
    }

    /** 
     * Gets orders connected to a particular delivery person using the 
     * `delivery_id`
     * @param string $delivery_id
     * @return array
    */
    public function get_orders_through_delivery($delivery_id){
        $delivery_id = mysqli_real_escape_string($this->conn,$delivery_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE delivery_id = '{$delivery_id}'");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /** 
     * Gets order using its id
     * @return array
    */
    public function get_order($Order_id){
        $Order_id = mysqli_real_escape_string($this->conn,$Order_id);
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE order_id = '{$Order_id}'");
        return mysqli_fetch_assoc($order_query);
    }
    
    /** 
     * Gets all orders
     * @return array
    */
    public function get_all_orders(){
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE 1");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /**
     * Gets all accepted orders
     * @return array
     */
    public function get_accepted_orders(){
        //TODO: do a unit test here
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE accepted = '1'");
        return mysqli_fetch_all($order_query,MYSQLI_ASSOC);
    }

    /**
     * Gets orders that have been accepted in specified packs
     * @param int $packet_number
     * @return array
     */
    public function get_accepted_orders_in_packets($packet_number){
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE accepted = '1'");
        $accepted_orders = array();
        for($i = 0; $i < $packet_number; $i++){
            $accepted_orders[$i] = mysqli_fetch_assoc($order_query);
        }
        return $accepted_orders;
    }
   
    /**
     * gets order data in ramndomied packs
     * if the data stored in the database is less than or 
     * equal to `$packet_number`, the whole order in the database is sent
     * @param int $packet_number
     * @return array
     */
    public function get_rangom_orders_in_packets($packet_number){
        $order_query = mysqli_query($this->conn, "SELECT * FROM orders WHERE 1");
        $all_orders = mysqli_fetch_all($order_query,MYSQLI_ASSOC);
        $number_of_data = mysqli_num_rows($order_query);
        $randomized_orders = array();
        if($number_of_data <= $packet_number){
            return $all_orders;
        }
        else{
            while($packet_number != 0){
                $replicas = 0;
                $some_random_index = random_int(0,count($all_orders)-1);
                if (in_array($some_random_index,$randomized_orders)) {
                    $replicas = $replicas + 1;
                }
                if($replicas == 0){
                    $randomized_orders[count($randomized_orders)] = $some_random_index;
                    $packet_number = $packet_number - 1;
                }
            }
            for($i = 0;$i < count($randomized_orders);$i++){
                $randomized_orders[$i] = $all_orders[$randomized_orders[$i]];
            }
            return $randomized_orders;
        }
         
    }

    /**
     * updates part of the order data
     * @param string $order_id
     * @param string $column_to_update
     * @param mixed $data
     * @return bool
     */
    public function update_order_data($order_id,$column_to_update,$data){
        if (is_string($data)) {
            return mysqli_query($this->conn,"UPDATE orders 
                                    SET {$column_to_update} = '{$data}'
                                    WHERE order_id = '{$order_id}'");
        }
        else{
            return mysqli_query($this->conn,"UPDATE orders 
                                    SET {$column_to_update} = {$data}
                                    WHERE order_id = '{$order_id}'");
        }
    }

    /**
     * delete an order using its id
     * @param string $order_id
     * @return bool
     */
    public function delete_order($order_id){
        return mysqli_query($this->conn,"DELETE FROM orders 
                                            WHERE  order_id = '{$order_id}'");
    }
    
}

?>