<?php

namespace Markt\orders;

include_once "dbconnections/order_database_connections.php";
include_once "dbconnections/order_location_tracking_database_connections.php";
include_once "products.php";

use Markt\DB\orderDB;
use Markt\DB\OrderLocationTracking;
use  Markt\Product;

/**
 * This class is responsible for Creating Orders, getting and setting order progress
 * deleting orders, creating order codes to be converted to qr, connecting to the order database
 */
class order{

    /**
     * the id of the order.
     * cannot be set directly and must not be set as such
     * @var string
     */
    private $order_id; 

    /**
     * the id of the seller. 
     * This is important as it would be used to accept the order
     * @var string
     */
    public $seller_id; 

    /**
     * the id of the buyer
     * @var string
     */
    public $buyer_id; 

    /**
     * the id of the delivery.
     * Set this using the `assign_delivery()` function so it gets saved in the database.
     * @var string
     */
    public $delivery_id; 

    /**
     * the id of the product
     * @var string
     */
    public $product_id; 

    /**
     * quantity of the product in the order
     * @var integer
     */
    public $product_quantity;

    /**
     * A check if the item has been collected by the delivery
     * @var boolean
     */
    private $received_by_delivery; 

    /**
     * the date the order was created. in the format d-m-y
     * @var string
     */
    private $order_date;

    /**
     * the cade to be converted to qr for scanning by the seller
     * @var string
     */
    private $receive_code; 

    /**
     * A check showing whether the item ordered has been delivered or not
     * @var boolean
     */
    private $delivered; 

    /**
     * the cade to be converted to qr for scanning by the seller
     * @var string
     */
    private $delivery_code; 

    /**
     * A check to see if the seller has accepted the order.
     * This is usually false when the order is created. 
     * In a rejection event (when the seller rejects the order), the order is deleted from the database.
     * Please call the `delete_order()` function in the case of a rejection event
     * @var boolean
     */
    private $accepted;

    /**
     * An array containing the longtitude, latitude and a string containing the 
     * house number, street, city and country seperated by commas
     * @var array
     */
    private $seller_precise_address;

    /**
     * An array containing the longtitude, latitude and a string containing the 
     * house number, street, city and country seperated by commas
     * @var array
     */
    private $buyer_precise_address;

    /**
     * A connection to the order database
     * @var object 
     */
    private $order_db;

    private $order_track_db;

    private $product_connect;

    /**
     * Order class constructor.
     * creates a new order if the `$order` is `null` and finds an order from the database if 
     * `$order` is specified
     * @param string|null $order the order id
     */
    public function __construct($order = null,$seller_precise_address = null,$buyer_precise_address = null) {
        $this->order_db = new orderDB();
        $this->order_track_db = new OrderLocationTracking();
        if(is_string($order)){
            $Set_order = $this->order_db->get_order($order);
            if(!empty($Set_order)){
                $this->create_order_from_database($Set_order);
            }
        }
        else{
            if(is_null($order) && is_array($seller_precise_address) && is_array($buyer_precise_address)){
                $this->order_id = $this->create_order_id();
                $this->delivery_code = $this->create_deliver_code();
                $this->receive_code = $this->create_receive_code();
                $this->buyer_precise_address = $buyer_precise_address;
                $this->seller_precise_address = $seller_precise_address;
            }
        }
    }

    /**
     * creates the order's receive code
     * @return string
     */
    private function create_receive_code(){
        if(!empty($this->receive_code)){
            return $this->receive_code;
        }
        else{
            $this->receive_code = uniqid("receivecode-",true);
            return $this->receive_code;
        }
    }

    /**
     * creates the order delivery code
     * @return string
     */
    private function create_deliver_code(){
        if(!empty($this->delivery_code)){
            return $this->delivery_code;
        }
        else{
            $this->delivery_code = uniqid("deliveredcode-",true);
            return $this->delivery_code;
        }
    }

    /**
     * creates a new order id
     * @return string
     */
    private function create_order_id(){
        return uniqid("order-",true);
    }

    /**
     * creates an order from the database
     * @param mixed $Order_array
     * @return void
     */
    private function create_order_from_database($Order_array){
        $this->seller_id = $Order_array["seller_id"];
        $this->order_id = $Order_array["order_id"];
        $this->buyer_id = $Order_array["buyer_id"];
        $this->product_id = $Order_array["product_id"];
        $this->delivery_id = $Order_array["delivery_id"];
        $this->product_quantity = $Order_array["product_quantity"];
        $this->delivered = $Order_array["delivered"];
        $this->accepted = $Order_array["accepted"];
        $this->delivery_code = $Order_array["delivery_code"];
        $this->receive_code = $Order_array["receive_code"];
        $this->received_by_delivery = $Order_array["received_by_delivery"];
        $this->order_date = $Order_array["order_date"];
    }

    /**
     * Creates an entirely new order
     * @return bool
     */
    public function create_new_order(){
        $new_order = array();
        if(!empty($this->buyer_id) && !empty($this->seller_id) && !empty($this->product_id)){
            $new_order["seller_id"] = $this->seller_id;
            $new_order["buyer_id"] = $this->buyer_id;
            $new_order["product_id"] = $this->product_id;
            $new_order["receive_code"] = $this->receive_code;
            $new_order["delivery_code"] = $this->delivery_code;
            $new_order["order_id"] = $this->order_id;
            $new_order["product_quantity"] = $this->product_quantity;
            $new_order["accepted"] = false;
            $new_order["delivered"] = false;
            $new_order["received_by_delivery"] = false;
            $new_order["order_date"] = date("Y-m-d");
            $new_order["delivery_id"] = "";
            return $this->order_db->create_order($new_order) && $this->create_order_track(
                                                            $this->seller_precise_address,
                                                            $this->buyer_precise_address);
        }
        else{
            return false;
        }
    }

    /**
     * gets an order from the database and returns said order
     * @param string $order_id
     * @return array
     */
    public function get_order($order_id = null){
        if(is_null($order_id)){
            return $this->order_db->get_order($this->order_id);
        }
        return $this->order_db->get_order($order_id);
    }

    /**
     * gets all orders from the databse that have been accepted by the seller
     * @return mixed
     */
    public function get_all_accepted_orders(){
        return $this->order_db->get_accepted_orders();
    }

    /**
     * Get all orders related to a seller
     * @param string $seller_id
     * @return array
     */
    public function get_seller_orders($seller_id = null){
        if(is_null($seller_id))
            return $this->order_db->get_orders_through_seller($this->seller_id);
        return $this->order_db->get_orders_through_seller($seller_id);
    }

    /**
     * Get all orders related to a seller
     * @param string $seller_id
     * @return array
     */
    public function get_seller_accepted_orders($seller_id = null){
        if(is_null($seller_id))
            return $this->order_db->get_accepted_orders_through_seller($this->seller_id);
        return $this->order_db->get_accepted_orders_through_seller($seller_id);
    }

    /**
     * gets all the orders that have been assigned a delivery person
     * @param mixed $delivery_id
     * @return array
     */
    public function get_all_orders_with_assigned_delivery($delivery_id){
        return $this->order_db->get_orders_through_delivery($delivery_id);
    }

    /**
     * checks if an order already has location tracking in the database.
     * returns true if it does and false if it does not
     * @param string $order_id
     * @return bool
     */
    public function has_location_tracking($order_id = null){
        if(is_null($order_id)){
            $location_tracks = $this->order_track_db->get_locations_using_order_id($this->order_id);
            return count($location_tracks) > 0;
        }
        $location_tracks = $this->order_track_db->get_locations_using_order_id($order_id);
        return count($location_tracks) > 0;
    }

    /**
     * Get all orders related to a seller
     * @param string $seller_id
     * @return array
     */
    public function get_seller_non_accepted_orders($seller_id = null){
        if(is_null($seller_id))
            return $this->order_db->get_non_accepted_orders_through_seller($this->seller_id);
        return $this->order_db->get_non_accepted_orders_through_seller($seller_id);
    }

    /**
     * Get all orders related to a buyer
     * @param string $buyer_id
     * @return array
     */
    public function get_buyer_orders($buyer_id = null){
        if(is_null($buyer_id))
            return $this->order_db->get_orders_through_buyer($this->buyer_id);
        return $this->order_db->get_orders_through_buyer($buyer_id);
    }

    /**
     * change the state of an order to accepted, the order should have been initiated from the 
     * constructor first.
     * @return boolean
     */
    public function accept_order(){
        $this->product_connect = new Product($this->product_id);
        $product_quantity_updated = $this->product_connect->update_product($this->product_id,
                                            "product_quantity",
                                            $this->product_connect->product_quantity-$this->product_quantity);
        $order_accepted = $this->order_db->update_order_data($this->order_id,"accepted",true);
        return $product_quantity_updated && $order_accepted;
    }

    /**
     * declines an order. An order is declined when the seller for some reason cannot continue or does 
     * want the transaction to proceed. A way to find out if an order has been declined is if the order
     * contains the buyer_id and no seller_id is present. `self::accepted` would be false (although 
     * this is not a way to check if an order has been declined since it would also be false when a 
     * seller has not yet accepted).
     * returns `true` on successful decline and `false` on non_successful decline
     * @return bool
     */
    public function decline_order(){
        return $this->order_db->update_order_data($this->order_id,"seller_id","");
    }

    /**
     * deletes an order if the using the order id set in the instance of the order class
     * @return boolean
     */
    public function delete_order(){
        $deleted_order = $this->order_db->delete_order($this->order_id);
        $deleted_order_track = $this->order_track_db->delete_locations_using_order_id($this->order_id);
        return $deleted_order && $deleted_order_track;
    }

    /**
     * assigns a delivery person to the order
     * @param mixed $delivery_id 
     * @return boolean
     */
    public function assign_delivery($delivery_id){
        $this->delivery_id = $delivery_id;
        return $this->order_db->update_order_data($this->order_id,"delivery_id",$delivery_id);
    }
    
    /**
     * checks if this instance of order has been declined
     * @return bool
     */
    public function is_order_declined(){
        return is_null($this->seller_id) || empty($this->seller_id) || !isset($this->seller_id);
    }

    /**
     * checks if this instance of order already has a delivery assigned to it
     * @return bool
     */
    public function is_delivery_assigned(){
        return !is_null($this->delivery_id) || !empty($this->delivery_id) || isset($this->delivery_id);
    }

    /**
     * Gets all user locations (buyer, seller and delivery). returns an array containing the following keys
     * order_id,buyer_latitude,buyer_longtitude,buyer_actual_address,seller_latitude,seller_longtitude
     * seller_actual_address,delivery_latitude,delivery_longtitude,delivery_actual_address
     * @return array|bool|null
     */
    public function get_user_locations(){
        return $this->order_track_db->get_locations_using_order_id($this->order_id);
    }

    /**
     * sets the location of the delivery. Can be used to track delivery progress
     * @param mixed $longtitude the longtitude of the delivery location
     * @param mixed $latitude the latitude of the delivery location
     * @return bool
     */
    public function set_delivery_location($longtitude,$latitude){
        $longtitude_set = $this->order_track_db->update_locations_using_order_id(
            $this->order_id,
            "delivery_longtitude",
            $longtitude);
        $latitude_set = $this->order_track_db->update_locations_using_order_id(
            $this->order_id,
            "delivery_latitude",
            $latitude
        );
        return $longtitude_set && $latitude_set;
    }

    /**
     * Creates a new order tracker in the database, the delivery addressess are set to null until a delivery 
     * personnel accepts or handles an order. `Seller_actual_address` and `buyer_actual_address` are not 
     * compulsory and would be set to empty strings if they are not provided
     * @param array $seller_precise_address An array containing the `buyer_longtitude`(float type),
     * `buyer_latitude` (float type) and (optionally)`buyer_actual_address`(string type)
     * @param array $buyer_precise_address An array containing the `seller_longtitude`(float type),
     * `seller_latitude` (float type) and (optionally)`seller_actual_address`(string type)
     * @return bool
     */
    private function create_order_track($seller_precise_address,$buyer_precise_address){
        $order_tracker = array();
        $order_tracker["order_id"] = $this->order_id;
        $order_tracker["seller_latitude"] = $seller_precise_address["latitude"];
        $order_tracker["seller_longtitude"] = $seller_precise_address["longtitude"];
        $order_tracker["buyer_latitude"] = $buyer_precise_address["latitude"];
        $order_tracker["buyer_longtitude"] = $buyer_precise_address["longtitude"];
        if(empty($seller_precise_address["seller_actual_address"]) && empty($buyer_precise_address["buyer_actual_address"])){
            $order_tracker["seller_actual_address"] = "";
            $order_tracker["buyer_actual_address"] = "";
        }
        else{
            $order_tracker["seller_actual_address"] = $seller_precise_address["seller_actual_address"];
            $order_tracker["buyer_actual_address"] = $buyer_precise_address["buyer_actual_address"];
        }
        $order_tracker["delivery_latitude"] = 0.0;
        $order_tracker["delivery_longtitude"] = 0.0;
        $order_tracker["delivery_actual_address"] = "";
        return $this->order_track_db->create_location_tracker($order_tracker);
    }

}

?>