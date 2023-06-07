<?php

namespace Markt;

include_once "dbconnections/product_database_connections.php";
include_once "dbconnections/product_images_database_connections.php";
include_once "image_handler.php";
include_once "seller.php";

use Markt\DB\ProductDB;
use Markt\DB\ProductImages;
use Markt\ImageHandler;
use Markt\Seller;

/**
 * Summary of Product
 */
class Product{

    /**
     * the id of the product, created automatically if constructor argument `$product_id` is not given
     * @var string
     */
    private $product_id; 

    /**
     * name of the product
     * @var string
     */
    public $product_name; 

    /**
     * the type of product, can only fall within two categories. If another category is added, 
     * such product would not be sent to the browser or show when queried
     * @var string
     */
    public $product_type; 

    /**
     * Product price
     * @var float
     */
    public $product_price; 

    /**
     * product description
     * @var string
     */
    public $product_description;

    /**
     * main category the product falls into
     * @var string
     */
    public $product_category;

    /**
     * product description tags
     * @var array
     */
    private $tags = [];

    /**
     * product quantity
     * @var int
     */
    public $product_quantity; 

    /**
     * estimated size of the product
     * @var integer
     */
    public $estimated_size;

    /**
     * The id of the seller of the product, must be set
     * @var string
     */
    public $seller_id; 

    /**
     * string containing description categories seperated by commas
     * @var string
     */
    public $desc_under;

    /**
     * an array containing the products images
     * @var array
     */
    public $this_products_images;

    /**
     * class to upload image descriptions to the database
     * @var \Markt\DB\ProductImages
     */
    private $product_images;

    /**
     * class to upload product to the database
     * @var \Markt\DB\ProductDB
     */
    private $product_database;

    /**
     * class to upload product images
     * @var \Markt\ImageHandler
     */
    private $image_uploader;

    /**
     * creates a new product instance
     * @param string $product_id the id of the product.
     * If specified, the class looks through the database for the existing product populates itself i.e
     * the class becomes the product and changes can be made to the product.If it is not specified, it is
     * assumed that a new product is being formed and a new product_id would be given to this instance
     */
    public function __construct($product_id = null) {
        $this->product_database = new ProductDB();
        $this->image_uploader = new ImageHandler();
        $this->product_images = new ProductImages();
        if(is_string($product_id)){
            $product = $this->product_database->get_product($product_id);
            if(is_array($product))
                $this->create_product_from_array($product);
        }
        elseif(is_null($product_id)){
            $this->product_id = $this->create_product_id();
        }
    }

    public function get_product_id(){
        return $this->product_id;
    }

    /**
     * Creates a new product in the database and saves the products image to the server and 
     * database respectively. returns true if creation was successful and false if not successful.
     * @return bool
     */
    public function create_product(){
        $product = array();
        $product["desc_under"] = $this->desc_under;
        $product["product_description"] = $this->product_description;
        $product["product_category"] = $this->product_category;
        $product["tags"] = "";
        foreach ($this->tags as $tag) {
            $product["tags"] = $product["tags"].$tag.",";
        }
        $product["product_id"] = $this->product_id;
        $product["product_name"] = $this->product_name;
        $product["product_price"] = $this->product_price;
        $product["product_quantity"] = $this->product_quantity;
        $product["estimated_size"] = $this->estimated_size;
        $product["product_type"] = $this->product_type;
        $product["seller_id"] = $this->seller_id;
        $this->create_product_images($this->this_products_images);
        return $this->product_database->create_product($product);
    }

    /**
     * deletes a product and its images from the server and database.
     * @param string $product_id The id of the product. If specified, the function would 
     * delete images and the product related to the product_id specified, if it is not specified,
     * the function would use the product_id in the instance of the class instead.
     * @return bool
     */
    public function delete_product($product_id = null){
        $product_images_deletion_complete = true;
        $product_image_desc_deletion_complete = true;
        if(is_null($product_id)){
            $all_product_images = $this->product_images->get_images_using_product_id($this->product_id);
            for($i = 0;$i < count($all_product_images);$i++){
                $image_deleted = $this->image_uploader->delete_product_image($all_product_images[$i]["image_name"]);
                $image_desc_deleted = $this->product_images->delete_image($all_product_images[$i]["image_id"]);
                if(!$image_deleted && !$image_desc_deleted){
                    $product_images_deletion_complete = false;
                    $product_image_desc_deletion_complete = false;
                }
            }
            $product_deleted = $this->product_database->delete_product($this->product_id);
            return $product_deleted && $product_images_deletion_complete && $product_image_desc_deletion_complete;
        }
        else{
            $all_product_images = $this->product_images->get_images_using_product_id($product_id);
            for($i = 0;$i < count($all_product_images);$i++){
                $image_deleted = $this->image_uploader->delete_product_image($all_product_images[$i]["image_name"]);
                $image_desc_deleted = $this->product_images->delete_image($all_product_images[$i]["image_id"]);
                if(!$image_deleted && !$image_desc_deleted){
                    $product_images_deletion_complete = false;
                    $product_image_desc_deletion_complete = false;
                }
            }
            $product_deleted = $this->product_database->delete_product($product_id);
            return $product_deleted && $product_images_deletion_complete && $product_image_desc_deletion_complete;
        }
    }

    /**
     * search for a product using its name or names and category
     * @param string $product_name
     * @param string $product_category
     * @return array
     */
    public function search_product($product_name,$product_category){
        if($product_name == null && $product_category == null){
            return $this->get_products(15);
        }
        elseif($product_name != null && $product_category == null){
            $searched_products = $this->product_database->get_products_with_name($product_name);
            $product_name = explode(" ",$product_name);
            if(count($product_name) < 2){
                return $searched_products;
            }
            else{
                foreach($product_name as $searched_product){
                    foreach($this->product_database->get_products_with_name($searched_product) as $part_product_name){
                        array_push($searched_products,$part_product_name);
                    }
                }
                return $searched_products;
            }
        }
        else{
            $searched_products = $this->product_database->get_products_with_name($product_name);
            return $searched_products;
        }
    }

    /**
     * gets all categories that have the tag specified
     * @param string $tag
     * @return array
     */
    private function check_tag_category($tag){
        if(file_exists("categories.json")){
            $tag = lcfirst($tag);
            $categories_json = file_get_contents("categories.json");
            $categories = json_decode($categories_json,true);
            $categories_with_tag = [];
            foreach($categories as $category){
                foreach($category["tags"] as $category_tag){
                    if($category_tag["name"] == $tag){
                        $categories_with_tag[count($categories_with_tag)] = $category["name"];
                    }
                }
            }
            return $categories_with_tag;
        }
        return [];
    }

    /**
     * gets products with a particular name and supplies them in packets of 15 based on `$index_num`.
     * if `$index_num` is greater than the amount of products provided then random products are supplied
     * and returned. if the `$product_category` is not supplied or is null, then the database is searched
     * for a product containing the `$product_name` only.
     * NOTE THAT if the `$index_num` is not changed, the same array chunk of data would be provided. 
     * `$index_num` just states the offset and point to get the data from and this is used in getting 
     * new data
     * @param string $product_name
     * @param string $product_category
     * @param integer $index_num
     * @return array
     */
    public function search_products_in_packets($product_name,$index_num = null,$product_category = null){
        if($index_num == null){
            if($product_category == null){
                return array_slice(
                    $this->product_database->get_products_with_name($product_name),
                    0,15
                );
            }
            else{
                return array_slice(
                    $this->product_database->get_products_with_specified_name_and_category(
                        $product_name,$product_category),
                    0,15
                );
            }
        }
        else{
            if($index_num > $this->product_database->get_amount_of_searched_products(
                                                $product_name,$product_category)){
                return $this->product_database->get_products_in_randomized_pack(15);
            }
            else{
                if($product_category == null){
                    return array_slice(
                        $this->product_database->get_products_with_name($product_name),
                        $index_num,
                        15
                    );
                }
                else{
                    return array_slice(
                        $this->product_database->get_products_with_specified_name_and_category(
                            $product_name,$product_category),
                        $index_num,
                        15
                    );
                }
            }
        }
    }

    /**
     * get random products from the database belonging to the same category as `$category`
     * @param string $category
     * @return array
     */
    public function get_products_from_category_in_packets($category){
        return $this->product_database->get_random_packets_of_product_using_category($category,15);
    }

    /**
     * creates a new product id
     * @return string
     */
    private function create_product_id(){
        return uniqid("product-",true);
    }

    /**
     * initializes and sets all attributes of the class to the ones contained in the array
     * @param array $product an array containing the details about the product
     * @return void
     */
    private function create_product_from_array($product){
        $this->desc_under = $product["desc_under"];
        $this->product_description = $product["product_description"];
        $this->product_category = $product["product_category"];
        $this->tags = explode(",",$product["tags"]);
        $this->product_id = $product["product_id"];
        $this->product_name = $product["product_name"];
        $this->product_price = $product["product_price"];
        $this->product_quantity = $product["product_quantity"];
        $this->estimated_size = $product["estimated_size"];
        $this->product_type = $product["product_type"];
        $this->seller_id = $product["seller_id"];
        $this->this_products_images = $this->get_images($product["product_id"]);
    }

    /**
     * Gets all images related to a particular product.
     * @param string $product_id if specified, then it gets all images related to the 
     * product id specified. If not specified, the function uses the `$product_id` from
     * the instance of the class
     * @return array
     */
    public function get_images($product_id = null){
        if(is_null($product_id))
            return $this->product_images->get_images_using_product_id($this->product_id);
        return $this->product_images->get_images_using_product_id($product_id);
    }

    /**
     * gets random products in packs 
     * @param int $amount the number of random products to get
     * @return array
     */
    public function get_products($amount){
        return $this->product_database->get_products_in_randomized_pack($amount);
    }

    /**
     * gets products close to a buyer using the buyer longtitude and latitude
     * @param float $buyer_longtitude
     * @param float $buyer_latitude
     * @param string $buyer_address
     * @return array
     */
    public function get_products_close_to_buyer($buyer_longtitude,$buyer_latitude,$buyer_address = null){
        $proximity = 100;
        $retrieved_products = [];
        $seller = new Seller();
        $retrieved_sellers = $seller->get_close_sellers($buyer_longtitude,$buyer_latitude,$proximity);
        foreach($retrieved_sellers as $retrieved_seller){
            $seller_products = $this->product_database->get_products_with_seller_id($retrieved_seller["unique_id"]);
            foreach($seller_products as $seller_product){
                $retrieved_products[count($retrieved_products)] = $seller_product;
            }
        }
        return $this->select_random_items($retrieved_products,15);
    }

    /**
     * selects random items from an array or list of items
     * @param array|list $item_set the list or array of items to select from
     * @param int $amount the amount of items to select. If this number is greater or equal to the
     * amount of items in `$item_set`, `$item_set` is returned untouched
     * @return array
     */
    private function select_random_items($item_set,$amount){
        if(count($item_set) <= $amount)
            return $item_set;
        $randomized_products = array();
        while($amount != 0){
            $replicas = 0;
            $some_random_index = random_int(0,count($item_set)-1);
            if (in_array($some_random_index,$randomized_products)) {
                $replicas = $replicas + 1;
            }
            if($replicas == 0){
                $randomized_products[count($randomized_products)] = $some_random_index;
                $amount = $amount - 1;
            }
        }
        for($i = 0;$i < count($randomized_products);$i++){
            $randomized_products[$i] = $item_set[$randomized_products[$i]];
        }
        return $randomized_products;
    }

    /**
     * gets all products related to a particular seller
     * @param string $seller_id
     * @return array
     */
    public function get_products_belonging_to_seller($seller_id = null){
        if(is_null($seller_id))
            return $this->product_database->get_products_with_seller_id($this->seller_id);
        return $this->product_database->get_products_with_seller_id($seller_id);
    }

    /**
     * updates part of a product
     * @param string $product_id
     * @param string $column_to_update
     * @param mixed $value
     * @return bool
     */
    public function update_product($product_id = null,$column_to_update,$value){
        if(is_null($product_id))
            return $this->product_database->update_product($this->product_id,$column_to_update,$value);
        return $this->product_database->update_product($product_id,$column_to_update,$value);
    }

    /**
     * create images for products and store them on the and the database,
     * The product_id has to be set first before this function is called
     * @param array $Product_images Contains the images from `$_FILES` that 
     * is to be uploaded.
     * @return void
     */
    private function create_product_images($Product_images){
        $all_images_names = $this->image_uploader->upload_multiple_images($Product_images);
        if(!empty($all_images_names)){
            for($i = 0;$i < count($all_images_names); $i++){
                $Image = array();
                $Image["image_id"] = uniqid("imageid-",true);
                $Image["image_name"] = $all_images_names[$i];
                $Image["product_id"] = $this->product_id;
                $Image["date_uploaded"] = date("Y-m-d");
                $this->product_images->create_image($Image);
            }
        }
    }

    /**
     * adds tags to the product
     * NOTE this function only adds the tags to the tags property of this instance of the product class
     * and does not add or save this in the database. To save the tags in the database, call the 
     * self::savetags() function after calling this function and adding the necessary tags
     * @param array|string $tags
     * @return void
     */
    public function add_tags($tags){
        if(is_array($tags)){
            foreach ($tags as $tag) {
                if(is_string($tag)){
                    $this->tags[count($this->tags)] = $tag;
                }
            }
        }
        elseif (is_string($tags)) {
            $this->tags[count($this->tags)] = $tags;
        }
    }

    public function get_tags(){
        return $this->tags;
    }

    /**
     * saves added tags to the database
     * @return bool
     */
    public function save_tags(){
        $tags_to_save = "";
        foreach ($this->tags as $tag) {
            $tags_to_save = $tags_to_save.$tag.",";
        }
        return $this->product_database->update_product($this->product_id,"tags",$tags_to_save);
    }

}

?>