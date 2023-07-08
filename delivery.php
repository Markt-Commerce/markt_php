<?php

namespace Markt;
require_once "dbconnections/user_database_connections.php";
include_once "orders.php";
include_once "dbconnections/order_location_tracking_database_connections.php";
include_once "image_handler.php";
include_once "dbconnections/payment_details_database_connections.php";

use Markt\DB\MarktDB;
use Markt\orders\order;
use Markt\DB\OrderLocationTracking;
use Markt\ImageHandler;
use Markt\DB\PaymentDetails;

/**
 * Everything related to delivery accounts,
 * i.e creation, logging in, checking deliveries, payments
 */
class delivery{

    /**
     * the id of the delivery
     * @var string
     */
    private $unique_id;

    /**
     * The name of the delivery
     * 
     * @var string
     */
    public $deliveryname;

    /** 
     * delivery Password,
     * must be hashed first with bcrypt or other hashing algorithm before storage
     * 
     * @var string
    */
    public $password;

    public $email;

    /** 
     * Used with latitude in determining the location of a delivery person.
     * Can change dynamically but is set when delivery account is created
     * 
     * @var float
    */
    public $longtitude;

    /** 
     * delivery location coordinates latitude
     * 
     * @var float
    */
    public $latitude;

    /**
     * delivery phone number
     * 
     * @var string
     */
    public $phone_number;

    /**
     * The type of vehicle the delivery person is using
     * types include `motorbike`, `car` ,`truck`, `bus`
     * and combinations of these
     * 
     * @var string
     */
    public $vehicle_type;

    /**
     * A boolean which states if a delivery person is working
     * for an a delivery organization/company or not
     * 
     * @var boolean
     */
    public $working_for_org;

    /**
     * The name of the organization the delivery person is 
     * working for if it exists. i.e `$working for org` is set
     * to true
     * 
     * @var string
     */
    public $org_name;

    /** 
     * stores the name of the profile image
     * 
     * @var string
    */
    public $profile_image;

    /**
     * delivery Payment details.
     * All payment transactions would be made with stripe as the 
     * method of payment. The connection to the stripe API would
     * be made in the payments.php file
     * 
     * @var array
     */
    public $delivery_payment_data_array;

    /** */
    public $house_number;

    /** */
    public $street;

    /** */
    public $city;

    /** */
    public $state;

    /** */
    public $country;

    /** */
    public $postal_code;

    /** */
    private $delivery_data;

    public $loggedin = false;

    /**
     * connection to the delivery database
     * @var \Markt\DB\MarktDB
     */
    private $delivery_db_connect;

    /**
     * connection to the orders for getting orders
     * @var \Markt\orders\order
     */
    private $delivery_orders_connect;

    /**
     * connection to the order locations for getting order locations
     * @var \Markt\DB\OrderLocationTracking
     */
    private $delivery_orders_locations_connect;

    /**
     * connection to the image uploader class
     * @var \Markt\ImageHandler
     */
    private $profile_image_handler;

    private $payments_db_connect;

    /**
     * array containing for hashing password
     * @var array
     */
    private $bycrypt_options = array('cost' => 14);

    /**
     * creates a new instance of delivery. If `$delivery` is a string i.e the id of a delivery, the database
     * is searched for a delivery with that id and the class is initialized with those details.
     * If `$delivery` is an array containing delivery personal information, the array should contain all data 
     * about the delivery. PLEASE NOTE that if a `unique_id` is provided in an array, it would be ignored and 
     * a new unique_id would be created.Therefore an array can only be provided if a new delivery is to be 
     * created and not for updating purposes. If the array however only contains the login information, the login
     * is populated with a user containing that information.
     * If however `$delivery` is not provided or is `null`, it would be assumed that a new delivery is being 
     * created and a new unique_id would be created. You can then access each property of the delivery class and
     * fill it with information. then call `create_new_delivery()` to save it to the database.
     * @param mixed $delivery
     */
    public function __construct($delivery = null) {
        $this->delivery_orders_locations_connect = new OrderLocationTracking();
        $this->delivery_orders_connect = new order();
        $this->profile_image_handler = new ImageHandler();
        $this->delivery_db_connect = new MarktDB();
        $this->payments_db_connect = new PaymentDetails();
        switch(true){
            case is_null($delivery):
                $this->initiate();
                break;
            case is_string($delivery):
                $this->initialize_from_db($delivery);
                break;
            case is_array($delivery) && count($delivery) > 2:
                $this->initiate();
                $this->initialize_from_array($delivery);
                break;
            case is_array($delivery) && count($delivery) == 2:
                $this->loggedin = $this->set_through_login($delivery);
        }
    }

    /**
     * Creates a new unique id and sets the `$unique_id` property of this instance to that unique id
     * @return void
     */
    private function initiate(){
        $this->unique_id = uniqid("delivery-",true);
    }

    /**
     * gets a delivery from the database and initializes the class with the gotten information
     * @param string $delivery_id
     * @return void
     */
    private function initialize_from_db($delivery_id){
        $delivery_data = $this->get_delivery_using_id($delivery_id);
        if(is_array($delivery_data)){
            $this->unique_id = $delivery_data["unique_id"];
            $this->initialize_from_array($delivery_data);
        }
    }

    /**
     * gets a delivery using its id
     * @param string $delivery_id
     * @return array
     */
    private function get_delivery_using_id($delivery_id){
        return $this->delivery_db_connect->get_delivery($delivery_id,"specific");
    }

    /**
     * initializes the class from an array containing the delivery details
     * @param array $delivery_array_data
     * @return void
     */
    private function initialize_from_array($delivery_array_data){
        $this->deliveryname = $delivery_array_data["deliveryname"];
        $this->password = $delivery_array_data["password"];
        $this->email = $delivery_array_data["email"];
        $this->longtitude = $delivery_array_data["longtitude"];
        $this->latitude = $delivery_array_data["latitude"];
        $this->phone_number = $delivery_array_data["phone_number"];
        $this->working_for_org = $delivery_array_data["working_for_org"];
        $this->org_name = $delivery_array_data["org_name"];
        $this->profile_image = $delivery_array_data["profile_image"];
        $this->delivery_payment_data_array = $this->get_delivery_payment_data();
        $this->house_number = $delivery_array_data["house_number"];
        $this->street = $delivery_array_data["street"];
        $this->city = $delivery_array_data["city"];
        $this->state = $delivery_array_data["state"];
        $this->country = $delivery_array_data["country"];
        $this->postal_code = $delivery_array_data["postal_code"];
    }

    private function get_delivery_payment_data(){
        return $this->payments_db_connect->get_accounts_belonging_to_delivery($this->unique_id);
    }

    /**
     * combines all properties of this class into an associative array and 
     * returns the array
     * @return array
     */
    private function compile(){
        $this->delivery_data = array();
        $this->delivery_data["unique_id"] = $this->unique_id;
        $this->delivery_data["deliveryname"] = $this->deliveryname;
        $this->delivery_data["password"] = password_hash($this->password,
                                                        PASSWORD_BCRYPT,
                                                        $this->bycrypt_options);
        $this->delivery_data["email"] = $this->email;
        $this->delivery_data["longtitude"] = $this->longtitude;
        $this->delivery_data["latitude"] = $this->latitude;
        $this->delivery_data["phone_number"] = $this->phone_number;
        $this->delivery_data["profile_image"] = $this->profile_image;
        $this->delivery_data["vehicle_type"] = $this->vehicle_type;
        $this->delivery_data["working_for_org"] = $this->working_for_org;
        $this->delivery_data["org_name"] = $this->org_name;
        $this->delivery_data["house_number"] = $this->house_number;
        $this->delivery_data["street"] = $this->street;
        $this->delivery_data["city"] = $this->city;
        $this->delivery_data["state"] = $this->state;
        $this->delivery_data["country"] = $this->country;
        $this->delivery_data["postal_code"] = $this->postal_code;
        return $this->delivery_data;
    }

    /**
     * returns an array containing details about the delivery.Gives an overview of this delivery instance's 
     * properties as an associative array
     * @return array
     */
    public function overview_summ(){
        $delivery_data = array();
        $delivery_data["deliveryname"] = $this->deliveryname;
        $delivery_data["email"] = $this->email;
        $delivery_data["phone_number"] = $this->phone_number;
        $delivery_data["profile_image"] = $this->profile_image;
        $delivery_data["vehicle_type"] = $this->vehicle_type;
        $delivery_data["working_for_org"] = $this->working_for_org;
        $delivery_data["org_name"] = $this->org_name;
        $delivery_data["house_number"] = $this->house_number;
        $delivery_data["street"] = $this->street;
        $delivery_data["city"] = $this->city;
        $delivery_data["state"] = $this->state;
        $delivery_data["country"] = $this->country;
        $delivery_data["postal_code"] = $this->postal_code;
        return $delivery_data;
    }

    /**
     * creates a new delivery
     * should be called after all public properties of this class instance has been set
     * @return bool
     */
    public function create_new_delivery(){
        if($this->already_in_database($this->email)){
            return false;
        }
        $this->save_payment_details($this->delivery_payment_data_array);
        return $this->delivery_db_connect->create_delivery($this->compile());
    }

    private function save_payment_details($PaymentDetails){
        $saved_all = true;
        foreach($PaymentDetails as $payment_data){
            try{
                $payment_data["user_type"] = "delivery";
                $payment_data["user_id"] = $this->unique_id;
                $this->payments_db_connect->create_payment_data($payment_data);
            }
            catch(\Exception $e){
                $saved_all = false;
            }
        }
        return $saved_all;
    }

    /**
     * adds a new delivery payment data to the database
     * @param array $payment_data
     * @return bool
     */
    public function add_payment_data($payment_data){
        $payment_data["user_type"] = "delivery";
        $payment_data["user_id"] = $this->unique_id;
        if($this->payments_db_connect->create_payment_data($payment_data)){
            $this->delivery_payment_data_array[count($this->delivery_payment_data_array)] = $payment_data;
            return true;
        }
        return false;
    }

    /**
     * gets delivery data from the database using the delivery id
     * @param mixed $delivery_id
     * @return mixed
     */
    public function get_delivery($delivery_id){
        return $this->delivery_db_connect->get_delivery($delivery_id,"specific");
    }

    /**
     * get the id of the delivery
     * @return string
     */
    public function get_delivery_id(){
        return $this->unique_id;
    }

    /**
     * checks if a delivery is already in the database
     * @param string $component
     * @return bool
     */
    public function already_in_database($component){
        $available_delivery = $this->delivery_db_connect->get_delivery_through_email($component);
        if(empty($available_delivery)){
            return false;
        }
            return true;
    }

    /**
     * initializes the instance of the class using the delivery login details.
     * Checks if there is an available login, if there is, it compares the password.
     * If both requirements are met, password is correct and email/deliveryname/phonenumber exists, 
     * the class is initialized.
     * returns true if initialization is successful and false if it is not.
     * @param mixed $delivery_login
     * @return bool
     */
    private function set_through_login($delivery_login){
        $user = $this->delivery_db_connect->get_delivery_through_username($delivery_login["deliverynameoremailorphonenumber"]);
        if(!empty($user)){
            if(password_verify($delivery_login["password"],$user["password"])){
                $this->unique_id = $user["unique_id"];
                $this->initialize_from_array($user);
                return true; 
            }
        }
        else{
            $user = $this->delivery_db_connect->get_delivery_through_email($delivery_login["deliverynameoremailorphonenumber"]);
            if(!empty($user)){
                if(password_verify($delivery_login["password"],$user["password"])){
                    $this->unique_id = $user["unique_id"];
                    $this->initialize_from_array($user);
                    return true;
                }
                return false;
            }
            else{
                $user = $this->delivery_db_connect->get_delivery_through_phone($delivery_login["deliverynameoremailorphonenumber"]);
                if(!empty($user)){
                    if(password_verify($delivery_login["password"],$user["password"])){
                        $this->unique_id = $user["unique_id"];
                        $this->initialize_from_array($user);
                        return true;
                    }
                    return false;
                }
            }
        }
        return false;
    }

     /**
      * Gets delivery orders i.e orders close to delivery person. Uses longtitude and latitude, so the
      * location tracker has to be on in the front_end. Incase this does not happen, do not call this 
      * function. Call the function to just get random orders if delivery location is not set.
      * @return array
      */
     public function get_delivery_orders(){
        $order_locations = [];
        $orders = [];
        $proximity = 200.0;
        $order_locations = $this->delivery_orders_locations_connect->get_close_order_locations(
                                                            $this->longtitude,
                                                            $this->latitude,
                                                            $proximity);
        for($i = 0; $i < count($order_locations); $i++){
            $orders[$i] = $this->delivery_orders_connect->get_order($order_locations[$i]["order_id"]);
        }
        return $this->filter_already_handled_deliveries($orders);
    }

    /**
     * removes orders that already have deliveries assigned or orders that a delivery account is
     * currently handling
     * @param array $deliveries
     * @return array
     */
    private function filter_already_handled_deliveries($deliveries){
        for($i = 0;$i < count($deliveries);$i++){
            if(!empty($deliveries[$i]["delivery_id"]) || $deliveries[$i]["accepted"] == 0){
                unset($deliveries[$i]);
            }
        }
        $filtered_orders = array_values($deliveries);
        return $filtered_orders;
    }

    /**
     * gets all deliveries that a delivery account is yet to attend to. A delivery order is unattended to
     * if it is not yet delivered or collected from a seller
     * @param mixed $delivery_id
     * @return array
     */
    public function get_pending_deliveries($delivery_id = null){
        if(is_null($delivery_id))
            return $this->delivery_orders_connect->get_all_orders_with_assigned_delivery($this->unique_id);
        return $this->delivery_orders_connect->get_all_orders_with_assigned_delivery($delivery_id);
    }

    /**
     * Uploads the profile image of the delivery
     * @param mixed $image
     * @return void
     */
    public function set_delivery_profile_image($image){
        $this->profile_image = $this->profile_image_handler->upload_image($image);
    }

     /**
     * Updates the profile image of the delivery
     * @param mixed $image
     * @return bool
     */
    public function update_delivery_profile_image($image){
        $deleted_old_profile_image = $this->profile_image_handler->delete_user_image($this->profile_image);
        $this->profile_image = $this->profile_image_handler->upload_image($image);
        $set_new_profile_image  = $this->update_delivery_detail("profile_image",$this->profile_image);
        return $deleted_old_profile_image && $set_new_profile_image;
    }

    /**
     * Changes the delivery password using the user email
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function change_password($email,$password){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            $delivery_details = $this->delivery_db_connect->get_delivery_through_email($email);
            if(count($delivery_details) > 0){
                return $this->delivery_db_connect->edit_delivery_data($delivery_details["unique_id"],
                                                        "password",
                                                        password_hash($password,PASSWORD_BCRYPT,$this->bycrypt_options));
            }
            else{
                return false;
            }
        }
        return false;
    }

    /**
     * updates a part of the delivery data
     * @param mixed $column
     * @param mixed $value
     * @param mixed $delivery_id
     * @return mixed
     */
    public function update_delivery_detail($column,$value,$delivery_id = null){
        if(is_null($delivery_id))
            return $this->delivery_db_connect->edit_delivery_data($this->unique_id,$column,$value);
        return $this->delivery_db_connect->edit_delivery_data($delivery_id,$column,$value);
    }

    /**
     * deletes payment data from the database
     * @param array $payment_data
     * @return \mysqli_result|bool
     */
    public function remove_payment_data($payment_data){
        $deleted = $this->payments_db_connect->delete_payment_data($this->unique_id,
                                                            "delivery",
                                                            $payment_data["card_number"],
                                                            $payment_data["payment_account_first_name"]);
        if($deleted){
            $this->delivery_payment_data_array = $this->get_delivery_payment_data();
        }
        return $deleted;
    }

    /**
     * deletes a delivery from the database
     * @return mixed
     */
    public function delete_delivery(){
        if($this->already_in_database($this->email)){
            return false;
        }
        return $this->delivery_db_connect->delete_delivery($this->unique_id);
    }
}

?>