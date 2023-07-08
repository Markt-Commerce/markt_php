<?php

namespace Markt;

include_once "dbconnections/user_database_connections.php";
include_once "orders.php";
include_once "products.php";
include_once "image_handler.php";
include_once "dbconnections/payment_details_database_connections.php";

use Markt\DB\MarktDB;
use Markt\Product;
use Markt\orders\order;
use Markt\ImageHandler; 
use Markt\DB\PaymentDetails;

/**
 * class for seller accounts including functions, user data e.t.c
 */
class Seller{

    /**
     * A unique id for the seller
     * @var string
     */
    private $unique_id;

    /**
     * The name of the shop
     * 
     * @var string
     */
    public $shopname;

    /** 
     * Shop Password,
     * must be hashed first with bcrypt or other hashing algorithm before storage
     * 
     * @var string
    */
    public $password;

    public $email;

    /** 
     * Used with latitude in determining the location of a seller shop.
     * Can change dynamically but is set when seller account is created
     * 
     * @var float
    */
    public $longtitude;

    /** 
     * seller location coordinates latitude
     * 
     * @var float
    */
    public $latitude;

    /**
     * seller phone number
     * 
     * @var string
     */
    public $phone_number;

    /** 
     * description about the shop and items they sell
     * 
     * @var string
    */
    public $description;

    /**
     * seller's market category
     * @var string
     */
    public $category;

    /** 
     * total number of rating by buyers who rated the shop
     * 
     * @var int
    */
    private $total_rating;

    /** 
     * total number of buyers or delivery who rated
     * 
     * @var int
    */
    private $total_raters;

    public $seller_rating;

    /** 
     * directions to the shop
     * 
     * @var string
    */
    public $directions;

    /** 
     * stores the name of the profile image
     * 
     * @var string
    */
    public $profile_image;

    /**
     * Shop Payment details.
     * All payment transactions would be made with stripe as the 
     * method of payment. The connection to the stripe API would
     * be made in the payments.php file
     * 
     * @var array
     */
    public $seller_payment_data_array;

    /** 
     * house/shop number of the seller
     * @var int
    */
    public $house_number;

    /** 
     * street the shop is located
     * @var string
    */
    public $street;

    /** 
     * city the shop is located
     * @var string
    */
    public $city;

    /** 
     * state the shop is located
     * @var string
    */
    public $state;

    /**
     * country the shop is located
     * @var string
     */
    public $country;

    /** 
     * address postal code
     * @var integer
    */
    public $postal_code;

    /** 
     * array containing seller info
     * @var array
    */
    private $seller_data;

    public $loggedin = false;

    private $seller_db_connect;

    /**
     * array containing for hashing password
     * @var array
     */
    private $bycrypt_options = array('cost' => 14);

    private $profile_image_handler;

    private $payments_db_connect;

    private $orders_connect;

    public $seller_orders;

    public $seller_products;

    /**
     * creates a new instance of seller. If `$seller` is a string i.e the id of a seller, the database
     * is searched for a seller with that id and the class is initialized with those details.
     * If `$seller` is an array containing seller personal information, the array should contain all data 
     * about the seller. PLEASE NOTE that if a `unique_id` is provided in an array, it would be ignored and 
     * a new unique_id would be created.Therefore an array can only be provided if a new seller is to be 
     * created and not for updating purposes. If the array however only contains the login information, the login
     * is populated with a user containing that information.
     * If however `$seller` is not provided or is `null`, it would be assumed that a new seller is being 
     * created and a new unique_id would be created. You can then access each property of the seller class and
     * fill it with information. then call `create_new_seller()` to save it to the database.
     * @param string|array|null $seller data about the seller.
     */
    public function __construct($seller = null) {
        $this->seller_db_connect = new MarktDB();
        $this->orders_connect = new order();
        $this->profile_image_handler = new ImageHandler();
        $this->payments_db_connect = new PaymentDetails();
        switch(true){
            case is_null($seller):
                $this->initiate();
                break;
            case is_string($seller):
                $this->initialize_from_db($seller);
                break;
            case is_array($seller) && count($seller) > 2:
                $this->initiate();
                $this->initialize_from_array($seller);
                break;
            case is_array($seller) && count($seller) == 2:
                $this->loggedin = $this->set_through_login($seller);
        }
    }

    /**
     * Creates a new unique id and sets the `$unique_id` property of this instance to that unique id
     * @return void
     */
    private function initiate(){
        $this->unique_id = uniqid("seller-",true);
        $this->total_raters = 0;
        $this->total_rating = 0;
    }

    /**
     * gets a seller from the database and initializes the class with the gotten information
     * @param string $seller_id
     * @return void
     */
    private function initialize_from_db($seller_id){
        $seller_data = $this->get_seller_using_id($seller_id);
        if(is_array($seller_data)){
            $this->unique_id = $seller_data["unique_id"];
            $this->seller_products = $this->get_seller_products(); 
            $this->total_raters = $seller_data["total_raters"];
            $this->total_rating = $seller_data["total_rating"];
            $this->initialize_from_array($seller_data);
            $this->seller_rating = $this->calculate_rating($this->total_rating,$this->total_raters);
        }
    }

    /**
     * initializes the class from an array containing the seller details
     * @param array $seller_array_data
     * @return void
     */
    private function initialize_from_array($seller_array_data){
        $this->shopname = $seller_array_data["shopname"];
        $this->password = $seller_array_data["password"];
        $this->email = $seller_array_data["email"];
        $this->longtitude = $seller_array_data["longtitude"];
        $this->latitude = $seller_array_data["latitude"];
        $this->phone_number = $seller_array_data["phone_number"];
        $this->description = $seller_array_data["description"];
        $this->category = $seller_array_data["category"];
        $this->directions = $seller_array_data["directions"];
        $this->profile_image = $seller_array_data["profile_image"];
        $this->seller_payment_data_array = $this->get_seller_payment_data();
        $this->house_number = $seller_array_data["house_number"];
        $this->street = $seller_array_data["street"];
        $this->city = $seller_array_data["city"];
        $this->state = $seller_array_data["state"];
        $this->country = $seller_array_data["country"];
        $this->postal_code = $seller_array_data["postal_code"];
    }

    private function get_seller_payment_data(){
        return $this->payments_db_connect->get_accounts_belonging_to_seller($this->unique_id);
    }

    public function get_seller_id(){
        return $this->unique_id;
    }

    /**
     * create an associative array containing seller details.
     * @return array
     */
    private function compile(){
        $this->seller_data = array();
        $this->seller_data["unique_id"] = $this->unique_id;
        $this->seller_data["shopname"] = $this->shopname;
        $this->seller_data["password"] = password_hash($this->password,
                                                        PASSWORD_BCRYPT,
                                                        $this->bycrypt_options);
        $this->seller_data["email"] = $this->email;
        $this->seller_data["longtitude"] = $this->longtitude;
        $this->seller_data["latitude"] = $this->latitude;
        $this->seller_data["phone_number"] = $this->phone_number;
        $this->seller_data["description"] = $this->description;
        $this->seller_data["category"] = $this->category;
        $this->seller_data["total_rating"] = $this->total_rating;
        $this->seller_data["total_raters"] = $this->total_raters;
        $this->seller_data["directions"] = $this->directions;
        $this->seller_data["profile_image"] = $this->profile_image;
        $this->seller_data["house_number"] = $this->house_number;
        $this->seller_data["street"] = $this->street;
        $this->seller_data["city"] = $this->city;
        $this->seller_data["state"] = $this->state;
        $this->seller_data["country"] = $this->country;
        $this->seller_data["postal_code"] = $this->postal_code;
        return $this->seller_data;
    }

    /**
     * returns an array containing details about the buyer.Gives an overview of this buyer instance's 
     * properties as an associative array
     * @return array
     */
    public function overview_summ(){
        $seller_data = array();
        $seller_data["shopname"] = $this->shopname;
        $seller_data["email"] = $this->email;
        $seller_data["phone_number"] = $this->phone_number;
        $seller_data["description"] = $this->description;
        $seller_data["category"] = $this->category;
        $seller_data["rating"] = $this->seller_rating;
        $seller_data["directions"] = $this->directions;
        $seller_data["profile_image"] = $this->profile_image;
        $seller_data["house_number"] = $this->house_number;
        $seller_data["street"] = $this->street;
        $seller_data["city"] = $this->city;
        $seller_data["state"] = $this->state;
        $seller_data["country"] = $this->country;
        $seller_data["postal_code"] = $this->postal_code;
        return $seller_data;
    }


    /**
     * creates a new seller
     * should be called after all public properties of this class instance has been set
     * @return bool
     */
    public function create_new_seller(){
        if($this->already_in_database($this->email)) 
            return false;
        $this->save_payment_details($this->seller_payment_data_array);
        return $this->seller_db_connect->create_Seller($this->compile());
    }

    /**
     * sets user payment data in the database.
     * returns true if all the data was saved and false if it was not. Would still return false if part 
     * of the data was saved.
     * @param array $PaymentDetails
     * @return bool
     */
    private function save_payment_details($PaymentDetails){
        $saved_all = true;
        foreach($PaymentDetails as $payment_data){
            try{
                $payment_data["user_type"] = "seller";
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
     * adds a new seller payment data to the database
     * @param array $payment_data
     * @return bool
     */
    public function add_payment_data($payment_data){
        $payment_data["user_type"] = "seller";
        $payment_data["user_id"] = $this->unique_id;
        if($this->payments_db_connect->create_payment_data($payment_data)){
            $this->seller_payment_data_array[count($this->seller_payment_data_array)] = $payment_data;
            return true;
        }
        return false;
    }

    /**
     * checks if a seller is already in the database
     * @param mixed $component
     * @return bool
     */
    public function already_in_database($component){
        $available_seller = $this->seller_db_connect->get_seller_through_email($component);
        return !empty($available_seller);
    }
    
    /**
     * calculate seller rating
     * @param mixed $total_ratings
     * @param mixed $total_raters
     * @return float|int
     */
    private function calculate_rating($total_ratings,$total_raters){
        if(is_null($total_raters) || $total_raters == 0)
                return 0;
        return round($total_ratings / $total_raters,1);
    }

    /**
     * changes the seller rating and saves it in the database
     * @param int $rating
     * @return bool
     */
    public function set_rating($rating){
        $this->total_rating = $this->total_rating + $rating;
        $this->total_raters = $this->total_raters + 1;
        $this->seller_rating = $this->calculate_rating($this->total_rating,$this->total_raters);
        $saved_rating = $this->seller_db_connect->edit_seller_data($this->unique_id,
                                                        "total_rating",
                                                        $this->total_rating);
        $saved_rater = $this->seller_db_connect->edit_seller_data($this->unique_id,
                                                            "total_raters",
                                                            $this->total_raters);
        return $saved_rating && $saved_rater;
    }

    /**
     * get a seller from the database using his id
     * and sets the details 
     * @param mixed $seller_id
     * @return mixed
     */
    public function get_seller_using_id($seller_id){
        return $this->seller_db_connect->get_Seller($seller_id,"specific");
    }

    /**
     * gets sellers close to a particular location
     * returns an associative array containing the sellers
     * @param float $latitude
     * @param float $longtitude
     * @param float $proximity
     * @return array
     */
    public function get_close_sellers($latitude,$longtitude,$proximity){
        return $this->seller_db_connect->get_close_sellers($latitude,$longtitude,$proximity);
    }

    /**
     * Changes the seller password using the user email
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function change_password($email,$password){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            $seller_details = $this->seller_db_connect->get_seller_through_email($email);
            if(count($seller_details) > 0){
                return $this->seller_db_connect->edit_seller_data($seller_details["unique_id"],
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
     * updates a seller detail in the database
     * @param string $column
     * @param mixed $value
     * @param string|null $seller_id
     * @return bool
     */
    public function update_seller_detail($column,$value,$seller_id = null){
        if(is_null($seller_id))
            return $this->seller_db_connect->edit_seller_data($this->unique_id,$column,$value);
        return $this->seller_db_connect->edit_seller_data($seller_id,$column,$value);
    }

    /**
     * initializes the instance of the class using the seller login details.
     * Checks if there is an available login, if there is, it compares the password.
     * If both requirements are met, password is correct and email exists, the class is initialized.
     * returns true if initialization is successful and false if it is not.
     * @param array $user_login_details
     * @return bool
     */
    private function set_through_login($user_login_details){
        $user = $this->seller_db_connect->get_seller_through_shopname($user_login_details["shopnameoremailorphonenumber"]);
        if(!empty($user)){
            if(password_verify($user_login_details["password"],$user["password"])){
                $this->unique_id = $user["unique_id"];
                $this->initialize_from_array($user);
                return true;
            }
        }
        else{
            $user = $this->seller_db_connect->get_seller_through_email($user_login_details["shopnameoremailorphonenumber"]);
            if(!empty($user)){
                if(password_verify($user_login_details["password"],$user["password"])){
                    $this->unique_id = $user["unique_id"];
                    $this->initialize_from_array($user);
                    return true;
                }
                return false;
            }
            else{
                $user = $this->seller_db_connect->get_seller_through_phone($user_login_details["shopnameoremailorphonenumber"]);
                if(!empty($user)){
                    if(password_verify($user_login_details["password"],$user["password"])){
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
     * Get all orders related to a seller
     * @param string $seller_id
     * @return array
     */
    public function get_seller_unaccepted_orders(){
        $this->orders_connect = new order();
        return $this->orders_connect->get_seller_non_accepted_orders($this->unique_id);
    } 

    /**
     * Uploads the profile image of the seller
     * @param mixed $image
     * @return void
     */
    public function set_seller_profile_image($image){
        $this->profile_image = $this->profile_image_handler->upload_image($image);
    }

     /**
     * Updates the profile image of the seller
     * @param mixed $image
     * @return bool
     */
    public function update_seller_profile_image($image){
        $deleted_old_profile_image = $this->profile_image_handler->delete_user_image($this->profile_image);
        $this->profile_image = $this->profile_image_handler->upload_image($image);
        $set_new_profile_image  = $this->update_seller_detail("profile_image",$this->profile_image);
        return $deleted_old_profile_image && $set_new_profile_image;
    }

    /**
     * gets all the products a seller sells
     * @return array
     */
    private function get_seller_products(){
        $seller_products = new Product();
        return $seller_products->get_products_belonging_to_seller($this->unique_id);
    }

    /**
     * deletes payment data from the database
     * @param array $payment_data
     * @return \mysqli_result|bool
     */
    public function remove_payment_data($payment_data){
        $deleted = $this->payments_db_connect->delete_payment_data($this->unique_id,
                                                            "seller",
                                                            $payment_data["card_number"],
                                                            $payment_data["payment_account_first_name"]);
        if($deleted){
            $this->seller_payment_data_array = $this->get_seller_payment_data();
        }
        return $deleted;
    }

    /**
     * deletes a seller
     * @return bool
     */
    public function delete_seller(){
        if($this->already_in_database($this->email)){
            return false;
        }
        return $this->seller_db_connect->delete_seller($this->unique_id);
    }

}

?>