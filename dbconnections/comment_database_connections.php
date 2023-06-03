<?php

namespace Markt\DB;
include_once "exceptions.php";
use Markt\MarktExceptions;

/**
 * Class for interacting with the comment database
 * and the creation , insertion and deletion of comments
 */
class commentDB{
    
    private $username = "root";
    private $host = "localhost";
    private $password = "";
    private $database = "users";
    private $conn;
    private $query_comment;

    /**
     * storage for errors from the error class
     * @var mixed
     */
    public $errors;


    /**
     * connects to the comments database 
     * and creates a table if it does not already exist.
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){
            $this->errors = new MarktExceptions();
            $this->errors->error();
        }
        else{
            $this->query_comment = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS comments (
            id INT NOT NULL AUTO_INCREMENT , comment_id VARCHAR(400) NOT NULL , 
            comment_title VARCHAR(255) NOT NULL , comment_place VARCHAR(255) NOT NULL , 
            comment_date DATE NOT NULL , comment_place_id VARCHAR(400) NOT NULL , 
            comment_body VARCHAR(400) NOT NULL , commenter VARCHAR(255) NOT NULL , 
            PRIMARY KEY (`id`))");
        }
    }

    /**
     * creates a new comment in the database
     * returns `true` if the creation was successfull and false if it was not
     * @param array $Comment
     * @return boolean
    */
    public function create_comment($Comment){
        $Comment["comment_id"] = mysqli_real_escape_string($this->conn,$Comment["comment_id"]);
        $Comment["comment_title"] = mysqli_real_escape_string($this->conn,$Comment["comment_title"]);
        $Comment["comment_place"] = mysqli_real_escape_string($this->conn,$Comment["comment_place"]);
        $Comment["comment_date"] = mysqli_real_escape_string($this->conn,$Comment["comment_date"]);
        $Comment["comment_place_id"] = mysqli_real_escape_string($this->conn,$Comment["comment_place_id"]);
        $Comment["comment_body"] = mysqli_real_escape_string($this->conn,$Comment["comment_body"]);
        $Comment["commenter"] = mysqli_real_escape_string($this->conn,$Comment["commenter"]);
        return mysqli_query($this->conn,"INSERT INTO comments(
            comment_id, comment_title, comment_place, 
            comment_date, comment_place_id, comment_body, commenter) 
            VALUES ('{$Comment["comment_id"]}','{$Comment["comment_title"]}',
            '{$Comment["comment_place"]}','{$Comment["comment_date"]}',
            '{$Comment["comment_place_id"]}','{$Comment["comment_body"]}',
            '{$Comment["commenter"]}')");
    }

    /**
     * fetches all comments from the database
     * returns an associative array containing the comments
     * @return array
    */
    public function get_all_comments(){
        $query = mysqli_query($this->conn,"SELECT * FROM comments WHERE 1");
        $all_comments = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $all_comments;
    }

     /**
     * fetches all comments from the database belonging to a particular seller
     * returns an associative array containing the comments
     * @return array
    */
    public function get_comments_using_seller($seller_id){
        $seller_id = mysqli_real_escape_string($this->conn,$seller_id);
        $query = mysqli_query($this->conn,"SELECT * FROM comments WHERE 
                                                comment_place_id = '{$seller_id}'");
        $all_comments = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $all_comments;
    }

     /**
     * fetches all comments from the database belonging to a particular product
     * returns an associative array containing the comments
     * @return array
    */
    public function get_comments_using_product($product_id){
        $product_id = mysqli_real_escape_string($this->conn,$product_id);
        $query = mysqli_query($this->conn,"SELECT * FROM comments WHERE 
                                        comment_place_id = '{$product_id}'");
        $all_comments = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $all_comments;
    }

    /**
     * fetches all comments from the database belonging to a particular product
     * returns an associative array containing the comments
     * @return array
    */
    public function get_comment($comment_id){
        $comment_id = mysqli_real_escape_string($this->conn,$comment_id);
        $query = mysqli_query($this->conn,"SELECT * FROM comments WHERE comment_id = '{$comment_id}'");
        $all_comments = mysqli_fetch_all($query,MYSQLI_ASSOC);
        return $all_comments;
    }

    /**
     * deletes a particular comment using its id
     * returns true if deletion was successful and false if it was not
     * @return boolean
    */
    public function delete_comment($comment_id){
        $comment_id = mysqli_real_escape_string($this->conn,$comment_id);
        return mysqli_query($this->conn,"DELETE FROM comments 
                                        WHERE comment_id = '{$comment_id}'");
    }

}

?>