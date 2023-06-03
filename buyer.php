<?php

namespace Markt;

require_once "dbconnections/user_database_connections.php";
require_once "image_handler.php";
require_once "dbconnections/cart_database_connections.php";
require_once "dbconnections/favorites_database_connections.php";
include_once "dbconnections/payment_details_database_connections.php";

use Exception;
use Markt\DB\MarktDB;
use Markt\ImageHandler;
use Markt\DB\CartDB;
use Markt\DB\FavoritesDB;
use Markt\DB\PaymentDetails;

/**
 * A class for working with buyer data 
 */
class buyer{

    /**
     * Summary of unique_id
     * @var
     */
    private $unique_id;

    /**
     * The name of the buyer
     * 
     * @var string
     */
    public $username;

    /** 
     * buyer Password,
     * must be hashed first with bcrypt or other hashing algorithm before storage
     * 
     * @var string
    */
    public $password;

    public $email;

    /** 
     * Used with latitude in determining the location of a buyer.
     * Can change dynamically but is set when buyer account is created
     * 
     * @var float
    */
    public $longtitude;

    /** 
     * buyer location coordinates latitude
     * 
     * @var float
    */
    public $latitude;

    /**
     * buyer phone number
     * 
     * @var string
     */
    public $phone_number;

    /** 
     * stores the name of the profile image
     * 
     * @var string
    */
    public $profile_image;

    /**
     * buyer Payment details.
     * All payment transactions would be made with stripe as the 
     * method of payment. The connection to the stripe API would
     * be made in the payments.php file
     * 
     * @var array
     */
    public $buyer_payment_data_array;

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
    private $buyer_data;

    public $loggedin = false;

    /**
     * buyer cart
     * @var array
     */
    public $cart = array();

    /**
     * collection of buyers favorites, sellers and products
     * @var array
     */
    public $favorites = array();

    /**
     * the allowed favorite types
     * @var array
     */
    private const allowed_favorites = ["product","seller"];

    /**
     * connection to the buyer database
     * @var \Markt\DB\MarktDB
     */
    private $buyer_db_connect;

    /**
     * connection to the image uploader class
     * @var \Markt\ImageHandler
     */
    private $profile_image_handler;

    /**
     * connection to the buyer cart db class
     * @var \Markt\DB\CartDB
     */
    private $buyer_cart_connect;

    /**
     * connection to the buyer favorites db class
     * @var \Markt\DB\FavoritesDB
     */
    private $buyer_favorites_connect;

    private $payments_db_connect;

    /**
     * array containing for hashing password
     * @var array
     */
    private $bycrypt_options = array('cost' => 14);

    
    /**
     * creates a new instance of buyer. If `$buyer` is a string i.e the id of a buyer, the database
     * is searched for a buyer with that id and the class is initialized with those details.
     * If `$buyer` is an array containing buyer personal information, the array should contain all data 
     * about the buyer. PLEASE NOTE that if a `unique_id` is provided in an array, it would be ignored and 
     * a new unique_id would be created.Therefore an array can only be provided if a new buyer is to be 
     * created and not for updating purposes. If the array however only contains the login information, the login
     * is populated with a user containing that information.
     * If however `$buyer` is not provided or is `null`, it would be assumed that a new buyer is being 
     * created and a new unique_id would be created. You can then access each property of the buyer class and
     * fill it with information. then call `create_new_buyer()` to save it to the database.
     * @param string|array|null $buyer data about the buyer.
     */
    public function __construct($buyer = null) {
        $this->profile_image_handler = new ImageHandler();
        $this->buyer_db_connect = new MarktDB();
        $this->buyer_cart_connect = new CartDB();
        $this->buyer_favorites_connect = new FavoritesDB();
        $this->payments_db_connect = new PaymentDetails();
        switch(true){
            case is_null($buyer):
                $this->initiate();
                break;
            case is_string($buyer):
                $this->initialize_from_db($buyer);
                $this->favorites = $this->get_favorites();
                $this->cart = $this->get_cart_items();
                break;
            case is_array($buyer) && count($buyer) > 2:
                $this->initiate();
                $this->initialize_from_array($buyer);
                break;
            case is_array($buyer) && count($buyer) == 2:
                $this->loggedin = $this->set_through_login($buyer);
                $this->favorites = $this->get_favorites();
                $this->cart = $this->get_cart_items();
        }
    }

    /**
     * Creates a new unique id and sets the `$unique_id` property of this instance to that unique id
     * @return void
     */
    private function initiate(){
        $this->unique_id = uniqid("buyer-",true);
    }

    /**
     * returns the unique_id of the instance of buyer
     * @return mixed|string
     */
    public function get_buyer_id(){
        return $this->unique_id;
    }

    /**
     * Summary of initialize_from_db
     * @param string $buyer_id
     * @return void
     */
    private function initialize_from_db($buyer_id){
        $buyer_data = $this->get_buyer_using_id($buyer_id);
        $this->unique_id = $buyer_data["unique_id"];
        $this->initialize_from_array($buyer_data);
    }

    /**
     * gets a buyer from the database and initializes the class with the gotten information
     * @param string $buyer_id
     * @return array
     */
    private function get_buyer_using_id($buyer_id){
        return $this->buyer_db_connect->get_buyer($buyer_id,"specific");
    }

    /**
     * initializes the class from an array containing the buyer details
     * @param array $buyer_array_data
     * @return void
     */
    private function initialize_from_array($buyer_array_data){
        $this->username = $buyer_array_data["username"];
        $this->password = $buyer_array_data["password"];
        $this->email = $buyer_array_data["email"];
        $this->longtitude = $buyer_array_data["longtitude"];
        $this->latitude = $buyer_array_data["latitude"];
        $this->phone_number = $buyer_array_data["phone_number"];
        $this->profile_image = $buyer_array_data["profile_image"];
        $this->buyer_payment_data_array = $this->get_buyer_payment_data();
        $this->house_number = $buyer_array_data["house_number"];
        $this->street = $buyer_array_data["street"];
        $this->city = $buyer_array_data["city"];
        $this->state = $buyer_array_data["state"];
        $this->country = $buyer_array_data["country"];
        $this->postal_code = $buyer_array_data["postal_code"];
    }

    private function get_buyer_payment_data(){
        return $this->payments_db_connect->get_accounts_belonging_to_buyer($this->unique_id);
    }

    /**
     * combines all properties of this class into an associative array and 
     * returns the array
     * @return array
     */
    private function compile(){
        $this->buyer_data = array();
        $this->buyer_data["unique_id"] = $this->unique_id;
        $this->buyer_data["username"] = $this->username;
        $this->buyer_data["password"] = password_hash($this->password,
                                                        PASSWORD_BCRYPT,
                                                        $this->bycrypt_options);
        $this->buyer_data["email"] = $this->email;
        $this->buyer_data["longtitude"] = $this->longtitude;
        $this->buyer_data["latitude"] = $this->latitude;
        $this->buyer_data["phone_number"] = $this->phone_number;
        $this->buyer_data["profile_image"] = $this->profile_image;
        $this->buyer_data["house_number"] = $this->house_number;
        $this->buyer_data["street"] = $this->street;
        $this->buyer_data["city"] = $this->city;
        $this->buyer_data["state"] = $this->state;
        $this->buyer_data["country"] = $this->country;
        $this->buyer_data["postal_code"] = $this->postal_code;
        return $this->buyer_data;
    }

    /**
     * creates a new buyer and stores the buyer in the database
     * @return bool
     */
    public function create_new_buyer(){
        if($this->already_in_database($this->email)){
            return false;
        }
        $this->save_payment_details($this->buyer_payment_data_array);
        return $this->buyer_db_connect->create_buyer($this->compile());
    }

    /**
     * Summary of save_payment_details
     * @param mixed $PaymentDetails
     * @return bool
     */
    private function save_payment_details($PaymentDetails){
        $saved_all = true;
        foreach($PaymentDetails as $payment_data){
            try{
                $payment_data["user_type"] = "buyer";
                $payment_data["user_id"] = $this->unique_id;
                $this->payments_db_connect->create_payment_data($payment_data);
            }
            catch(Exception $e){
                $saved_all = false;
            }
        }
        return $saved_all;
    }

    /**
     * adds a new buyer payment data to the database
     * @param array $payment_data
     * @return bool
     */
    public function add_payment_data($payment_data){
        $payment_data["user_type"] = "buyer";
        $payment_data["user_id"] = $this->unique_id;
        if($this->payments_db_connect->create_payment_data($payment_data)){
            $this->buyer_payment_data_array[count($this->buyer_payment_data_array)] = $payment_data;
            return true;
        }
        return false;
    }

    /**
     * gets a buyer from the database using the buyer's id
     * @param string $buyer_id
     * @return array
     */
    public function get_buyer($buyer_id){
        return $this->buyer_db_connect->get_buyer($buyer_id,"specific");
    }

    /**
     * checks if a buyer is already in the database
     * @param string $component
     * @return bool
     */
    public function already_in_database($email){
        $available_buyer = $this->buyer_db_connect->get_buyer_through_email($email);
        if(empty($available_buyer)){
            return false;
        }
            return true;
    }

    /**
     * initializes the instance of the class using the buyer login details.
     * Checks if there is an available login, if there is, it compares the password.
     * If both requirements are met, password is correct and email/username/phonenumber exists, 
     * the class is initialized.
     * returns true if initialization is successful and false if it is not.
     * @param array $buyer_login
     * @return bool
     */
    private function set_through_login($buyer_login){
        $user = $this->buyer_db_connect->get_buyer_through_username($buyer_login["usernameoremailorphonenumber"]);
        if(!empty($user)){
            if(password_verify($buyer_login["password"],$user["password"])){
                $this->unique_id = $user["unique_id"];
                $this->initialize_from_array($user);
                return true;
            }
            return false;
        }
        else{
            $user = $this->buyer_db_connect->get_buyer_through_email($buyer_login["usernameoremailorphonenumber"]);
            if(!empty($user)){
                if(password_verify($buyer_login["password"],$user["password"])){
                    $this->unique_id = $user["unique_id"];
                    $this->initialize_from_array($user);
                    return true;
                }
                return false;
            }
            else{
                $user = $this->buyer_db_connect->get_buyer_through_phone($buyer_login["usernameoremailorphonenumber"]);
                if(!empty($user)){
                    if(password_verify($buyer_login["password"],$user["password"])){
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
     * Changes the buyer password using the user email
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function change_password($email,$password){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            $buyer_details = $this->buyer_db_connect->get_buyer_through_email($email);
            if(count($buyer_details) > 0){
                return $this->buyer_db_connect->edit_buyer_data($buyer_details["unique_id"],
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
     * Gets buyer favorite items - A Seller or a Product
     * @return array
     */
    private function get_favorites(){
        $favs = $this->buyer_favorites_connect->get_buyer_favorites($this->unique_id);
        return $favs;
    }

    /**
     * Adds a favorite to the database
     * A favorite can either be a seller or product
     * @param array $Favorite
     * @return bool
     */
    public function add_favorite($Favorite){
        $favorite_exists = $this->buyer_favorites_connect->favorite_exists($this->unique_id,
                                                                        $Favorite["favorite_type"],
                                                                        $Favorite["favorite_id"]);
        if(!$favorite_exists && in_array($Favorite["favorite_type"],self::allowed_favorites)){
            $Favorite["buyer_id"] = $this->unique_id;
            $Favorite["unique_id"] = uniqid("favorite-",true);
            $this->favorites[count($this->favorites)] = $Favorite;
            return $this->buyer_favorites_connect->add_favorite($Favorite);
        }
        return false;
    }

    /**
     * removes a seller or product favorite from the database
     * @param string $favorite_id
     * @return \mysqli_result|bool
     */
    public function remove_favorite($favorite_id){
        return $this->buyer_favorites_connect->remove_favorite($favorite_id);
    }

    /**
     * Adds an item to a buyer cart
     * If `$item` is an array, the array should be in this format:
     * $item["product_id"]
     * $item["quantity"].
     * However, if `$item` is a string, the string would be taken as the product_id of the product
     * to be added to the cart
     * @param array|string $item
     * @return bool
     */
    public function add_to_cart($item){
        //TODO: THIS CODE NEEDS TESTING 
        if(is_string($item)){
            $item_exists = $this->buyer_cart_connect->item_already_exists($this->unique_id,$item);
            if($item_exists){
                for($i = 0;$i < count($this->cart);$i++){
                    if($this->cart[$i]["product_id"] == $item){
                        $this->cart[$i]["quantity"] = $this->cart[$i]["quantity"] + 1;
                        return $this->buyer_cart_connect->update_quantity(
                                                            $this->cart[$i]["product_id"],
                                                                $this->cart[$i]["quantity"]
                        );
                    }
                }
        }
        else{
            $new_cart_item = array();
            $new_cart_item["cart_id"] = uniqid("item-",true);
            $new_cart_item["buyer_id"] = $this->unique_id;
            $new_cart_item["product_id"] = $item;
            $new_cart_item["quantity"] = 1;
            return $this->buyer_cart_connect->create_cart_item($new_cart_item);
        }
    }
        elseif(is_array($item)){
            $item_exists = $this->buyer_cart_connect->item_already_exists($this->unique_id,$item["product_id"]);
            if($item_exists){
                for($i = 0;$i < count($this->cart);$i++){
                    if($this->cart[$i]["product_id"] == $item["product_id"]){
                        $this->cart[$i]["quantity"] += $item["quantity"];
                        return $this->buyer_cart_connect->update_quantity(
                                                            $this->cart[$i]["product_id"],
                                                                $this->cart[$i]["quantity"]
                        );
                    }
                }
            }
            else{
                $new_cart_item = array();
                $new_cart_item["cart_id"] = uniqid("item-",true);
                $new_cart_item["buyer_id"] = $this->unique_id;
                $new_cart_item["product_id"] = $item["product_id"];
                $new_cart_item["quantity"] = $item["quantity"];
                return $this->buyer_cart_connect->create_cart_item($new_cart_item);
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_cart_items(){
        return $this->buyer_cart_connect->get_buyer_cart_items($this->unique_id);
    }

    /**
     * removes an item from the cart
     * @param string|array $item the cart item to remove. If it is a string, the string is considered 
     * as the cart_id and the item is removed from the cart based on it. If it is an array, the 
     * cart_id is checked for in the array and is used for the deletion.
     * @return \mysqli_result|bool
     */
    public function remove_from_cart($item){
        if(is_string($item))
            return $this->buyer_cart_connect->remove_buyer_cart_item($item);
        return $this->buyer_cart_connect->remove_buyer_cart_item($item["cart_id"]);
    }

    /**
     * Uploads the profile image of the buyer
     * @param mixed $image
     * @return void
     */
    public function set_buyer_profile_image($image){
        $this->profile_image = $this->profile_image_handler->upload_image($image);
    }

    /**
     * changes or updates part of a seller detail
     * @param string $column
     * @param mixed $value
     * @param string $buyer_id
     * @return bool
     */
    public function update_buyer_detail($column,$value,$buyer_id = null){
        if(is_null($buyer_id))
            return $this->buyer_db_connect->edit_buyer_data($this->unique_id,$column,$value);
        return $this->buyer_db_connect->edit_buyer_data($buyer_id,$column,$value);
    }

    /**
     * deletes payment data from the database
     * @param array $payment_data
     * @return \mysqli_result|bool
     */
    public function remove_payment_data($payment_data){
        $deleted = $this->payments_db_connect->delete_payment_data($this->unique_id,
                                                            "buyer",
                                                            $payment_data["card_number"],
                                                            $payment_data["payment_account_first_name"]);
        if($deleted){
            $this->buyer_payment_data_array = $this->get_buyer_payment_data();
        }
        return $deleted;
    }

    /**
     * deletes a buyer from the database
     * @return bool
     */
    public function delete_buyer(){
        if($this->already_in_database($this->email)){
            return false;
        }
        return $this->buyer_db_connect->delete_buyer($this->unique_id);
    }
    
}

?>