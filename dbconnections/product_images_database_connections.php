<?php

namespace Markt\DB;
include_once "exceptions.php";
use Markt\MarktExceptions;

/**
 * Summary of ProductImages
 */
class ProductImages{

    /**
     * the username to the database
     * @var string
     */
    private $username = "root";
    /**
     * the domain hosting the database
     * @var string
     */
    private $host = "localhost";
    /**
     * database password
     * @var string
     */
    private $password = "";
    /**
     * Summary of database
     * @var string
     */
    private $database = "users";
    /**
     * to be used for all connections to the database
     * @var object
     */
    private $conn;
    /**
     * Summary of query_product_image
     * @var
     */
    private $query_product_image;

    /**
     * storage for errors from the error class
     * @var mixed
     */
    public $errors;

    /**
     */
    public function __construct() {
        $this->conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);
        if(!$this->conn){
            $this->errors = new MarktExceptions();
            $this->errors->error();
        }
        else{
            $this->query_product_image = mysqli_query($this->conn,"CREATE TABLE IF NOT EXISTS productimages (
                id INT NOT NULL AUTO_INCREMENT , image_id VARCHAR(400) NOT NULL , 
                image_name VARCHAR(400) NOT NULL , product_id VARCHAR(400) NOT NULL, 
                date_uploaded DATE NOT NULL , PRIMARY KEY (`id`))");
        }
    }

    /**
     * creates a new image with its name in the database
     * The keys in image include image_id, image_name, product_id and date_uploaded
     * @param array $Image
     * @return boolean
     */
    public function create_image($Image){
        return mysqli_query($this->conn,"INSERT INTO productimages(
        image_id, image_name, product_id, date_uploaded) 
        VALUES ('{$Image["image_id"]}','{$Image["image_name"]}',
        '{$Image["product_id"]}','{$Image["date_uploaded"]}')");
    }

    /**
     * gets all image names and descriptions from the database
     * @return array
     */
    public function get_all_images(){
        $query = mysqli_query($this->conn,"SELECT * FROM productimages WHERE 1");
        return mysqli_fetch_all($query,MYSQLI_ASSOC);
    }

    /**
     * get all images belonging to a particular product
     * @return array
     */
    public function get_images_using_product_id($product_id){
        $query = mysqli_query($this->conn,"SELECT * FROM productimages WHERE product_id = '{$product_id}'");
        return mysqli_fetch_all($query,MYSQLI_ASSOC);
    }

    /**
     * gets a particular image belonging using its id
     * @return array
     */
    public function get_image_using_id($image_id){
        $query = mysqli_query($this->conn,"SELECT * FROM productimages WHERE image_id = '{$image_id}'");
        return mysqli_fetch_assoc($query);
    }

    /**
     * deletes an image using its id
     * @param string $image_id
     * @return boolean
     */
    public function delete_image($image_id){
        return mysqli_query($this->conn,"DELETE FROM productimages WHERE image_id = '{$image_id}'");
    }
}

?>