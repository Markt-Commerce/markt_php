<?php

namespace Markt\DB;

/**
 * Class that deals with the storage, update, retrieval and deletion of
 * user payment data
 */
class PaymentDetails{
    
    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;
    private $query_payment;

    /**
     * storage for errors from the error class
     * @var mixed
     */
    public $errors;

    /**
     * constructor to Markt's database creation
     * creates a new connection to the database
     * creates payment tables for `seller`,`buyer` and `delivery` if they do not exist
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){
            
        }
        else{
            $this->query_payment = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS payment_details (
                id INT NOT NULL AUTO_INCREMENT , user_id VARCHAR(400) NOT NULL , 
                user_type VARCHAR(255) NOT NULL , payment_account_first_name VARCHAR(255) NOT NULL , 
                payment_account_last_name VARCHAR(255) NOT NULL , 
                payment_account_number VARCHAR(255) NOT NULL , 
                card_number VARCHAR(255) NOT NULL , card_expiry_date VARCHAR(255) NOT NULL , 
                cvc INT NOT NULL , PRIMARY KEY (`id`))");
        }
    }

    /**
     * Creates new payment data belonging to a user i.e `buyer`, `seller`,`delivery`
     * in the database
     * @param array $PaymentData
     * @return \mysqli_result|bool
     */
    public function create_payment_data($PaymentData){
        return mysqli_query($this->conn,"INSERT INTO payment_details
        (user_id, user_type, payment_account_first_name, payment_account_last_name, 
        payment_account_number, card_number, card_expiry_date, cvc) 
        VALUES ('{$PaymentData["user_id"]}','{$PaymentData["user_type"]}',
        '{$PaymentData["payment_account_first_name"]}','{$PaymentData["payment_account_last_name"]}',
        '{$PaymentData["payment_account_number"]}','{$PaymentData["card_number"]}',
        '{$PaymentData["card_expiry_date"]}','{$PaymentData["cvc"]}')");
    }

    /**
     * gets all payment data saved in the database that belongs to a particular buyer
     * using the buyer id
     * @param string $buyer_id
     * @return array
     */
    public function get_accounts_belonging_to_buyer($buyer_id){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $buyer_accounts_query = mysqli_query($this->conn,"SELECT * FROM payment_details WHERE
                                                        user_type = 'buyer' 
                                                        AND user_id = '{$buyer_id}'");
        return mysqli_fetch_all($buyer_accounts_query,MYSQLI_ASSOC);
    }

    /**
     * gets all payment data saved in the database that belongs to a particular seller
     * using the seller id
     * @param string $seller_id
     * @return array
     */
    public function get_accounts_belonging_to_seller($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $seller_accounts_query = mysqli_query($this->conn,"SELECT * FROM payment_details WHERE
                                                        user_type = 'seller' 
                                                        AND user_id = '{$seller_id}'");
        return mysqli_fetch_all($seller_accounts_query,MYSQLI_ASSOC);
    }

    /**
     * gets all payment data saved in the database that belongs to a particular delivery
     * using the delivery id
     * @param string $delivery_id
     * @return array
     */
    public function get_accounts_belonging_to_delivery($delivery_id){
        $delivery_id = mysqli_real_escape_string($this->conn,$delivery_id);
        $delivery_accounts_query = mysqli_query($this->conn,"SELECT * FROM payment_details WHERE
                                                        user_type = 'delivery' 
                                                        AND user_id = '{$delivery_id}'");
        return mysqli_fetch_all($delivery_accounts_query,MYSQLI_ASSOC);
    }

    /**
     * Gets all the payment data from the database
     * @return array
     */
    public function get_all_payment_data(){
        $all_payments_query = mysqli_query($this->conn,"SELECT * FROM payment_details WHERE 1");
        return mysqli_fetch_all($all_payments_query,MYSQLI_ASSOC);
    }

    /**
     * Edits payment details belonging to a buyer using the `$buyer_id`
     * @param string $buyer_id the id of the buyer
     * @param string $column_name the column to update
     * @param mixed $value the value to that will be used to update
     * @return \mysqli_result|bool
     */
    public function update_buyer_payment_data($buyer_id,$column_name,$value){
        $buyer_id = mysqli_real_escape_string($this->conn,$buyer_id);
        $column_name = mysqli_real_escape_string($this->conn,$column_name);
        if(is_string($value))
            return mysqli_query($this->conn,"UPDATE payment_details
                    SET {$column_name} = '{$value}' WHERE user_type = 'buyer' AND 
                                                            user_id = '{$buyer_id}'");
        return mysqli_query($this->conn,"UPDATE payment_details
                SET {$column_name} = {$value} WHERE user_type = 'buyer' AND 
                                                        user_id = '{$buyer_id}'");
    }

    /**
     * Edits payment details belonging to a seller using the `$seller_id`
     * @param string $seller_id the id of the seller
     * @param string $column_name the column to update
     * @param mixed $value the value to that will be used to update
     * @return \mysqli_result|bool
     */
    public function update_seller_payment_data($seller_id,$column_name,$value){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $column_name = mysqli_real_escape_string($this->conn,$column_name);
        if(is_string($value))
            return mysqli_query($this->conn,"UPDATE payment_details
                    SET {$column_name} = '{$value}' WHERE user_type = 'seller' AND 
                                                                user_id = '{$seller_id}'");
        return mysqli_query($this->conn,"UPDATE payment_details
                SET {$column_name} = {$value} WHERE user_type = 'seller' AND 
                                                            user_id = '{$seller_id}'");
    }

    /**
     * Edits payment details belonging to a delivery using the `$delivery_id`
     * @param string $delivery_id the id of the delivery
     * @param string $column_name the column to update
     * @param mixed $value the value to that will be used to update
     * @return \mysqli_result|bool
     */
    public function update_delivery_payment_data($delivery_id,$column_name,$value){
        $delivery_id = mysqli_real_escape_string($this->conn,$delivery_id);
        $column_name = mysqli_real_escape_string($this->conn,$column_name);
        if(is_string($value))
            return mysqli_query($this->conn,"UPDATE payment_details
                    SET {$column_name} = '{$value}' WHERE user_type = 'delivery' AND
                                                            user_id = '{$delivery_id}'");
        return mysqli_query($this->conn,"UPDATE payment_details
                SET {$column_name} = {$value} WHERE user_type = 'delivery' AND 
                                                        user_id = '{$delivery_id}'");
    }

    /**
     * Delete payment data of any of the users `buyer`, `seller`, `delivery`
     * based on the `user_id` and `user_type`
     * @param string $user_id
     * @param string $user_type
     * @param string $card_number
     * @param string $payment_first_name
     * @return \mysqli_result|bool
     */
    public function delete_payment_data($user_id,$user_type,$card_number,$payment_first_name){
        $user_id = mysqli_real_escape_string($this->conn,$user_id);
        $user_type = mysqli_real_escape_string($this->conn,$user_type);
        $card_number = mysqli_real_escape_string($this->conn,$card_number);
        $payment_first_name = mysqli_real_escape_string($this->conn,$payment_first_name);
        return mysqli_query($this->conn,"DELETE FROM payment_details WHERE user_type = '{$user_type}' 
                                        AND user_id = '{$user_id}' AND card_number = '{$card_number}'
                                        AND payment_account_first_name = '{$payment_first_name}'");
    }

}

?>