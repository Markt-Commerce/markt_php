<?php

namespace Markt\DB;

/**
 * Class for connecting to the order location database,
 * saves, updates, modifies and deletes the locations of users during a transaction/order
 */
class OrderLocationTracking{

    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;

    private $query_location_tracker;

    /**
     * connects to the database and creates a table if it does not already exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if($this->conn){
            $this->query_location_tracker = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS 
            order_location_tracking (id INT NOT NULL AUTO_INCREMENT , order_id VARCHAR(400) NOT NULL , 
            seller_latitude FLOAT NOT NULL , seller_longtitude FLOAT NOT NULL , 
            seller_actual_address VARCHAR(400) NOT NULL , buyer_latitude FLOAT NOT NULL , 
            buyer_longtitude FLOAT NOT NULL , buyer_actual_address VARCHAR(400) NOT NULL , 
            delivery_latitude FLOAT NOT NULL , delivery_longtitude FLOAT NOT NULL , 
            delivery_actual_address VARCHAR(400) NOT NULL , PRIMARY KEY (id))");
        }
    }

    /**
     * create a new entry in the database with the location of users in a particular
     * order.
     * @param array $Tracker
     * @return bool
     */
    public function create_location_tracker($Tracker){
        return mysqli_query($this->conn,"INSERT INTO order_location_tracking(
            order_id, seller_latitude, seller_longtitude, seller_actual_address, 
            buyer_latitude, buyer_longtitude, buyer_actual_address, delivery_latitude, 
            delivery_longtitude, delivery_actual_address) VALUES (
                '{$Tracker["order_id"]}','{$Tracker["seller_latitude"]}',
                '{$Tracker["seller_longtitude"]}','{$Tracker["seller_actual_address"]}',
                '{$Tracker["buyer_latitude"]}','{$Tracker["buyer_longtitude"]}',
                '{$Tracker["buyer_actual_address"]}','{$Tracker["delivery_latitude"]}',
                '{$Tracker["delivery_longtitude"]}','{$Tracker["delivery_actual_address"]}')");
    }

    /**
     * gets the locations of users relating to a particular order using the 
     * order id
     * @param string $order_id
     * @return array|bool|null
     */
    public function get_locations_using_order_id($order_id){
        $query = mysqli_query($this->conn,"SELECT * FROM order_location_tracking WHERE order_id = '{$order_id}'");
        $locations = mysqli_fetch_assoc($query);
        return $locations;
    }

    /**
     * Get orders to a seller close to a location based on the proximity
     * @param float $longtitude the longtitude of the user from which the order locations would be determined
     * @param float $latitude the latitude of the user from which the order locations would be determined
     * @param float $proximity the range or distance or area between the location and location of the order
     * @return array
     */
    public function get_close_order_locations($longtitude,$latitude,$proximity){
        $longtitude_range_max = $longtitude + $proximity;
        $latitude_range_max = $latitude + $proximity;
        $longtitude_range_min = $longtitude - $proximity;
        $latitude_range_min = $latitude - $proximity;
        $query = mysqli_query($this->conn,"SELECT * FROM order_location_tracking 
                                        WHERE seller_latitude BETWEEN 
                                        {$latitude_range_min} AND {$latitude_range_max}
                                        AND seller_longtitude BETWEEN 
                                        {$longtitude_range_min} AND {$longtitude_range_max}");
        $result = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $result;
    }

    /**
     * updates the locations of users based on the user id
     * @param string $order_id
     * @param string $column_to_be_updated
     * @param mixed $value
     * @return bool
     */
    public function update_locations_using_order_id($order_id,$column_to_be_updated,$value){
        if(!is_string($value)){
            return mysqli_query($this->conn,"UPDATE order_location_tracking
            SET {$column_to_be_updated} = {$value}
            WHERE order_id = {$order_id}");
        }
        return mysqli_query($this->conn,"UPDATE order_location_tracking
        SET {$column_to_be_updated} = {$value}
        WHERE order_id = '{$order_id}'");
    }

    /**
     * delete an entry of user locations related to a particalar 
     * order in the database using the order_id
     * @param string $order_id
     * @return \mysqli_result|bool
     */
    public function delete_locations_using_order_id($order_id){
        return mysqli_query($this->conn,"DELETE FROM order_location_tracking WHERE order_id = '{$order_id}'");
    }

}

?>