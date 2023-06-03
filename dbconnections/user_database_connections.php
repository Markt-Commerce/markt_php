<?php

namespace Markt\DB;

//include_once "exceptions.php";
//use Markt\MarktExceptions;

/**
 * Summary of MarktDB
 */
class MarktDB{
    
    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;
    private $query_buyer;
    private $query_seller;
    private $query_delivery;

    /**
     * storage for errors from the error class
     * @var mixed
     */
    public $errors;

    /**
     * constructor to Markt's database creation
     * creates a new connection to the database
     * creates all the user tables `seller`,`buyer`, `delivery` if they do not exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){
            //$this->errors = new MarktExceptions();
            //$this->errors->error();
        }
        else{
            $this->query_seller = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS sellers (
                id INT NOT NULL AUTO_INCREMENT, unique_id VARCHAR(400) NOT NULL , 
                shopname VARCHAR(255) NOT NULL , password VARCHAR(255) NOT NULL ,
                email VARCHAR(255) NOT NULL , longtitude FLOAT NOT NULL , 
                latitude FLOAT NOT NULL , phone_number VARCHAR(255) NOT NULL , 
                description VARCHAR(400) NOT NULL , category VARCHAR(255) NOT NULL , 
                total_rating INT NOT NULL , total_raters INT NOT NULL , 
                directions VARCHAR(400) NOT NULL , profile_image VARCHAR(400) NOT NULL , 
                house_number INT NOT NULL , street VARCHAR(255) NOT NULL , 
                city VARCHAR(255) NOT NULL , state VARCHAR(255) NOT NULL , country VARCHAR(255) NOT NULL , 
                postal_code INT NOT NULL , PRIMARY KEY (`id`))");
       
               $this->query_buyer = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS buyers (
                   id INT NOT NULL AUTO_INCREMENT, unique_id VARCHAR(400) NOT NULL , 
                   username VARCHAR(255) NOT NULL , password VARCHAR(255) NOT NULL , 
                   email VARCHAR(255) NOT NULL , longtitude FLOAT NOT NULL , 
                   latitude FLOAT NOT NULL , profile_image VARCHAR(400) NOT NULL , 
                   phone_number VARCHAR(255) NOT NULL , house_number INT NOT NULL , street VARCHAR(255) NOT NULL , 
                   city VARCHAR(255) NOT NULL , state VARCHAR(255) NOT NULL , country VARCHAR(255) NOT NULL , 
                   postal_code INT NOT NULL , PRIMARY KEY (`id`))");
       
               $this->query_delivery = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS delivery (
                   id INT NOT NULL AUTO_INCREMENT, unique_id VARCHAR(400) NOT NULL , 
                   deliveryname VARCHAR(255) NOT NULL , password VARCHAR(255) NOT NULL , 
                   email VARCHAR(255) NOT NULL , longtitude FLOAT NOT NULL , latitude FLOAT NOT NULL , 
                   vehicle_type VARCHAR(255) NOT NULL , working_for_org BOOLEAN NOT NULL , 
                   org_name VARCHAR(255) NOT NULL , profile_image VARCHAR(400) NOT NULL , 
                   phone_number VARCHAR(255) NOT NULL , house_number INT NOT NULL , 
                   street VARCHAR(255) NOT NULL , city VARCHAR(255) NOT NULL , state VARCHAR(255) NOT NULL , 
                   country VARCHAR(255) NOT NULL , postal_code INT NOT NULL , PRIMARY KEY (`id`))");
        }
    }

    /**
     * creates a new buyer account in the database
     * returns `true` if the account was saved successfully
     * and `false` if it was not.
     * after this function is called and it returns `false`,
     * call `get_error_type` to display the error type
     * @param mixed $BuyerDetails
     * @return boolean
     */
    public function create_Buyer($BuyerDetails){
        return mysqli_query($this->conn,"INSERT INTO buyers
        (unique_id, username, password, email, longtitude, latitude, 
        profile_image, phone_number, house_number, street, city, state, 
        country, postal_code) 
        VALUES ('{$BuyerDetails["unique_id"]}','{$BuyerDetails["username"]}',
        '{$BuyerDetails["password"]}','{$BuyerDetails["email"]}',
        '{$BuyerDetails["longtitude"]}','{$BuyerDetails["latitude"]}',
        '{$BuyerDetails["profile_image"]}','{$BuyerDetails["phone_number"]}',
        '{$BuyerDetails["house_number"]}',
        '{$BuyerDetails["street"]}','{$BuyerDetails["city"]}','{$BuyerDetails["state"]}',
        '{$BuyerDetails["country"]}','{$BuyerDetails["postal_code"]}')");
    }

    /**
     * creates a new seller account in the database
     * returns `true` if the account was saved successfully
     * and `false` if it was not.
     * after this function is called and it returns `false`,
     * call `get_error_type` to display the error type
     * @param mixed $SellerDetails
     * @return boolean
     */
    public function create_Seller($SellerDetails){
        return mysqli_query($this->conn,"INSERT INTO sellers
        (unique_id, shopname, password, email, longtitude, latitude,
        phone_number, description, category, total_rating, total_raters, 
        directions, profile_image, house_number, 
        street, city, state, country, postal_code) 
        VALUES ('{$SellerDetails["unique_id"]}','{$SellerDetails["shopname"]}',
        '{$SellerDetails["password"]}','{$SellerDetails["email"]}',
        '{$SellerDetails["longtitude"]}','{$SellerDetails["latitude"]}',
        '{$SellerDetails["phone_number"]}','{$SellerDetails["description"]}',
        '{$SellerDetails["category"]}','{$SellerDetails["total_rating"]}','{$SellerDetails["total_raters"]}',
        '{$SellerDetails["directions"]}','{$SellerDetails["profile_image"]}',
        '{$SellerDetails["house_number"]}','{$SellerDetails["street"]}',
        '{$SellerDetails["city"]}','{$SellerDetails["state"]}','{$SellerDetails["country"]}',
        '{$SellerDetails["postal_code"]}')");
    }

    /**
     * creates a new delivery account in the database
     * returns `true` if the account was saved successfully
     * and `false` if it was not.
     * after this function is called and it returns `false`,
     * call `get_error_type` to display the error type
     * @param mixed $DeliveryDetails
     * @return boolean
     */
    public function create_Delivery($DeliveryDetails){
        return mysqli_query($this->conn,"INSERT INTO delivery 
        (unique_id, deliveryname, password, email, longtitude, latitude,
        vehicle_type, working_for_org, org_name, profile_image, 
        house_number, street, city, state, country, postal_code, phone_number) 
        VALUES ('{$DeliveryDetails["unique_id"]}','{$DeliveryDetails["deliveryname"]}',
        '{$DeliveryDetails["password"]}','{$DeliveryDetails["email"]}',
        '{$DeliveryDetails["longtitude"]}','{$DeliveryDetails["latitude"]}',
        '{$DeliveryDetails["vehicle_type"]}','{$DeliveryDetails["working_for_org"]}',
        '{$DeliveryDetails["org_name"]}','{$DeliveryDetails["profile_image"]}',
        '{$DeliveryDetails["house_number"]}','{$DeliveryDetails["street"]}',
        '{$DeliveryDetails["city"]}','{$DeliveryDetails["state"]}',
        '{$DeliveryDetails["country"]}','{$DeliveryDetails["postal_code"]}',
        '{$DeliveryDetails["phone_number"]}')");
    }

    /** 
     * get seller from the database depending on the type specified.
     * if the `id` is specified or `amount_type` is set to `specific`,
     * then it gets a specific seller. if `id` is not specified 
     * it gets all the sellers in the database, if `amount_type` is set to `packets`, it 
     * gets specified amounts of random users based on the `$amount` parameter
     * @param int $id 
     * @param mixed $amount_type 
     * @param int $amount 
     * @return mixed
    */
    public function get_Seller($id = null,$amount_type = "all",$amount = 0){
        $seller_data = null;
        switch($amount_type){
            case "all":
                $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE 1");
                $seller_data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                break;
            case "specific":
                if($id == null){
                    //$exct = new MarktExceptions();
                    //$exct->error();
                }
                else{
                    $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE unique_id = '{$id}'");
                    if(mysqli_num_rows($query) > 0){
                        $seller_data = mysqli_fetch_assoc($query);
                    }
                    else{
                        //$exct = new MarktExceptions();
                        //$exct->error();
                    }
                }
                break;
            case "packets":
                $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE 1");
                $data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                $number_of_data = mysqli_num_rows($query);
                $randomized_data = array();
                if($number_of_data <= $amount){
                    $seller_data = $data;
                }
                else{
                    while($amount != 0){
                        $replicas = 0;
                        $some_random_index = random_int(0,count($data)-1);
                        for($i = 0;$i < count($randomized_data);$i++){
                            if($randomized_data[$i] == $some_random_index){
                                $replicas = $replicas + 1;
                            }
                        }
                        if($replicas == 0){
                            $randomized_data[count($randomized_data)] = $some_random_index;
                            $amount = $amount - 1;
                        }
                    }
                    for($i = 0;$i < count($randomized_data);$i++){
                        $randomized_data[$i] = $data[$randomized_data[$i]];
                    }
                    $seller_data = $randomized_data;
                }
                break;
            default:
            $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE 1");
            $seller_data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                break;
        }
        return $seller_data;
    }


    /** 
     * get buyer from the database depending on the type specified.
     * if the `id` is specified or `amount_type` is set to `specific`,
     * then it gets a specific buyer. if `id` is not specified 
     * it gets all the buyers in the database, if `amount_type` is set to `packets`, it 
     * gets specified amounts of random buyers based on the `$amount` parameter
     * @param int $id 
     * @param mixed $amount_type 
     * @param int $amount 
     * @return mixed
    */
    public function get_Buyer($id = null,$amount_type = "all",$amount = 0){
        $buyer_data = null;
        switch($amount_type){
            case "all":
                $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE 1");
                $buyer_data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                break;
            case "specific":
                if($id == null){
                    //$exct = new MarktExceptions();
                    //$exct->error();
                }
                else{
                    $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE unique_id = '{$id}'");
                    if(mysqli_num_rows($query) > 0){
                        $buyer_data = mysqli_fetch_assoc($query);
                    }
                    else{
                        //$exct = new MarktExceptions();
                        //$exct->error();
                    }
                }
                break;
            case "packets":
                $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE 1");
                $data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                $number_of_data = mysqli_num_rows($query);
                $randomized_data = array();
                if($number_of_data <= $amount){
                    $buyer_data = $data;
                }
                else{
                    while($amount != 0){
                        $replicas = 0;
                        $some_random_index = random_int(0,count($data)-1);
                        for($i = 0;$i < count($randomized_data);$i++){
                            if($randomized_data[$i] == $some_random_index){
                                $replicas = $replicas + 1;
                            }
                        }
                        if($replicas == 0){
                            $randomized_data[count($randomized_data)] = $some_random_index;
                            $amount = $amount - 1;
                        }
                    }
                    for($i = 0;$i < count($randomized_data);$i++){
                        $randomized_data[$i] = $data[$randomized_data[$i]];
                    }
                    $buyer_data = $randomized_data;
                }
                break;
            default:
            $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE 1");
            $buyer_data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                break;
        }
        return $buyer_data;
    }



    /** 
     * get delivery peron from the database depending on the type specified.
     * if the `id` is specified or `amount_type` is set to `specific`,
     * then it gets a specific delivery person. if `id` is not specified 
     * it gets all the delivery people in the database, if `amount_type` is set to `packets`, it 
     * gets specified amounts of random delivery people based on the `$amount` parameter
     * @param int $id 
     * @param mixed $amount_type 
     * @param int $amount 
     * @return mixed
    */
    public function get_Delivery($id = null,$amount_type = "all",$amount = 0){
        $delivery_data = null;
        switch($amount_type){
            case "all":
                $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE 1");
                $delivery_data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                break;
            case "specific":
                if($id == null){
                    //$exct = new MarktExceptions();
                    //$exct->error();
                }
                else{
                    $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE unique_id = '{$id}'");
                    if(mysqli_num_rows($query) > 0){
                        $delivery_data = mysqli_fetch_assoc($query);
                    }
                    else{
                        //$exct = new MarktExceptions();
                        //$exct->error();
                    }
                }
                break;
            case "packets":
                $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE 1");
                $data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                $number_of_data = mysqli_num_rows($query);
                $randomized_data = array();
                if($number_of_data <= $amount){
                    $delivery_data = $data;
                }
                else{
                    while($amount != 0){
                        $replicas = 0;
                        $some_random_index = random_int(0,count($data)-1);
                        for($i = 0;$i < count($randomized_data);$i++){
                            if($randomized_data[$i] == $some_random_index){
                                $replicas = $replicas + 1;
                            }
                        }
                        if($replicas == 0){
                            $randomized_data[count($randomized_data)] = $some_random_index;
                            $amount = $amount - 1;
                        }
                    }
                    for($i = 0;$i < count($randomized_data);$i++){
                        $randomized_data[$i] = $data[$randomized_data[$i]];
                    }
                    $delivery_data = $randomized_data;
                }
                break;
            default:
            $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE 1");
            $delivery_data = mysqli_fetch_all($query,MYSQLI_ASSOC);
                break;
        }
        return $delivery_data;
    }

    /**
     * gets a seller from the database that has the same shopname as the one provided
     * returns false if the shopname does not exist and an array of it does
     * @param string $shopname
     * @return array|bool
     */
    public function get_seller_through_shopname($shopname){
        $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE shopname = '{$shopname}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
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
        $longtitude_range_max = $longtitude + $proximity;
        $latitude_range_max = $latitude + $proximity;
        $longtitude_range_min = $longtitude - $proximity;
        $latitude_range_min = $latitude - $proximity;
        $seller_query = mysqli_query($this->conn,"SELECT unique_id FROM sellers 
                                                WHERE latitude BETWEEN 
                                                {$latitude_range_min} AND {$latitude_range_max}
                                                AND longtitude BETWEEN 
                                                {$longtitude_range_min} AND {$longtitude_range_max}");
        $seller_result = mysqli_fetch_all($seller_query,MYSQLI_ASSOC);
        return $seller_result;
    }

    /**
     * gets a buyer from the database that has the same username as the one provided
     * returns false if the username does not exist and an array of it does
     * @param string $username
     * @return array|bool
     */
    public function get_buyer_through_username($username){
        $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE username = '{$username}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a delivery from the database that has the same deliveryname as the one provided
     * returns false if the deliveryname does not exist and an array of it does
     * @param string $deliveryname
     * @return array|bool
     */
    public function get_delivery_through_username($deliveryname){
        $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE deliveryname = '{$deliveryname}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a seller from the database that has the same email as the one provided
     * returns false if the email does not exist and an array of it does
     * @param string $email
     * @return array|bool
     */
    public function get_seller_through_email($email){
        $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE email = '{$email}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a buyer from the database that has the same username as the one provided
     * returns false if the email does not exist and an array of it does
     * @param string $email
     * @return array|bool
     */
    public function get_buyer_through_email($email){
        $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE email = '{$email}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a delivery from the database that has the same email as the one provided
     * returns false if the email does not exist and an array of it does
     * @param string $email
     * @return array|bool
     */
    public function get_delivery_through_email($email){
        $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE email = '{$email}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a seller from the database that has the same phone number as the one provided
     * returns false if the phone number does not exist and an array of it does
     * @param string $phone
     * @return array|bool
     */
    public function get_seller_through_phone($phone){
        $query = mysqli_query($this->conn,"SELECT * FROM sellers WHERE phone_number = '{$phone}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a buyer from the database that has the same phone number as the one provided
     * returns false if the phone number does not exist and an array of it does
     * @param string $phone
     * @return array|bool
     */
    public function get_buyer_through_phone($phone){
        $query = mysqli_query($this->conn,"SELECT * FROM buyers WHERE phone_number = '{$phone}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * gets a delivery from the database that has the same phone number as the one provided
     * returns false if the phone number does not exist and an array of it does
     * @param string $phone
     * @return array|bool
     */
    public function get_delivery_through_phone($phone){
        $query = mysqli_query($this->conn,"SELECT * FROM delivery WHERE phone_number = '{$phone}'");
        if(mysqli_num_rows($query) <= 0)return false;
        $data = mysqli_fetch_assoc($query);
        return $data;
    }

    /**
     * updates data in the database depending on the type of data, the type of user e.t.c
     * @param string $id
     * @param string $user_account_type
     * @param string $column_name
     * @param mixed $data
     * @return boolean
     */
    private function update_data($id,$user_account_type,$column_name,$data){
        $column_name = mysqli_real_escape_string($this->conn,$column_name);
        if (!is_string($data)) {
            return mysqli_query($this->conn,"UPDATE {$user_account_type}
                                SET {$column_name} = {$data} WHERE unique_id = '{$id}'");
        }
        return mysqli_query($this->conn,"UPDATE {$user_account_type} 
                                SET {$column_name} = '{$data}' WHERE unique_id = '{$id}'");
    }

    /**
     * edit some part of the seller data
     * returns `true` if editing was possible or `false` if it was not
     * @return boolean
     * @param int $id the user_id of the seller
     * @param string $part_of_data means the name of the column in the database i.e name,password, email 
     * @param mixed $value the value to enter into the column
    */
    public function edit_seller_data($id,$part_of_data,$value){
        return $this->update_data($id,"sellers",$part_of_data,$value);
    }

    /**
     * edit some part of the buyer data
     * returns `true` if editing was possible or `false` if it was not
     * @return boolean
     * @param int $id
     * @param string $part_of_data
     * @param mixed $value
    */
    public function edit_buyer_data($id,$part_of_data,$value){
        return $this->update_data($id,"buyers",$part_of_data,$value);
    }

    /**
     * edit some part of the delivery person data
     * returns `true` if editing was possible or `false` if it was not
     * @return boolean
     * @param int $id
     * @param string $part_of_data
     * @param mixed $value
    */
    public function edit_delivery_data($id,$part_of_data,$value){
        return $this->update_data($id,"delivery",$part_of_data,$value);
    }

    /**
     * deletes data in the database depending on the type of data, the type of user e.t.c
     * returns true if deletion was successful
     * @param int $id
     * @return boolean
     */
    private function delete_user($id,$user_account_type){
        return mysqli_query($this->conn,"DELETE FROM {$user_account_type} 
                                            WHERE  unique_id = '{$id}'");
    }

    /**
     * deletes data in the database depending on the type of data, the type of user e.t.c
     * returns true if deletion was successful
     * @param int $id
     * @return boolean
     */
    public function delete_buyer($id){
        return $this->delete_user($id,"buyers");
    }

    /**
     * deletes data in the database depending on the type of data, the type of user e.t.c
     * returns true if deletion was successful
     * @param int $id
     * @return boolean
     */
    public function delete_seller($id){
        return $this->delete_user($id,"sellers");
    }

    /**
     * deletes data in the database depending on the type of data, the type of user e.t.c
     * returns true if deletion was successful
     * @param int $id
     * @return boolean
     */
    public function delete_delivery($id){
        return $this->delete_user($id,"delivery");
    }

}

?>