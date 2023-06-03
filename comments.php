<?php

namespace Markt;

include_once "dbconnections/comment_database_connections.php";

use Markt\DB\commentDB;

/**
 * Class responsible for creating and adding comments to or under a product or seller, 
 * deleting comments, e.t.c
 */
class Comment{

    /**
     * The id of the comment
     * @var string
     */
    private $comment_id;

    /**
     * Title of the comment (optional)
     * @var string
     */
    public $comment_title;

    /**
     * The place the comment was uploaded. Can either be of two types `Product` or `Seller`
     * @var string
     */
    public $comment_place;

    /**
     * The date the comment was added
     * @var string
     */
    private $comment_date;

    /**
     * the id of the comment place
     * @var string
     */
    public $comment_place_id;

    /**
     * the comment itself (details about the comment)
     * @var string
     */
    public $comment_body;
    
    /**
     * the name of the buyer that made the comment (not id)
     * @var string
     */
    public $commenter;

    /**
     * connection to the comment database
     * @var 
     */
    public $comment_database;


    /**
     * Summary of __construct
     */
    public function __construct(){
        $this->comment_database = new commentDB();
        $this->comment_id = uniqid("comment-",true);
        $this->comment_date = date("d-m-Y h:i:s");
    }

    /**
     * Summary of create_comment
     * @return bool
     */
    public function create_comment(){
        if(!empty($this->comment_body) && !empty($this->comment_place) && !empty($this->comment_place_id) 
        && !empty($this->comment_title) && !empty($this->commenter) ){
            $new_comment = array();
            $new_comment["comment_body"] = $this->comment_body;
            $new_comment["comment_date"] = $this->comment_date;
            $new_comment["comment_id"] = $this->comment_id;
            $new_comment["comment_place"] =  $this->comment_place;
            $new_comment["comment_place_id"] =  $this->comment_place_id;
            $new_comment["comment_title"] =  $this->comment_title;
            $new_comment["commenter"] =  $this->commenter;
            return $this->comment_database->create_comment($new_comment);
        }
        return false;
    }

    /**
     * Summary of get_comment
     * @param mixed $comment_id
     * @return array
     */
    public function get_comment($comment_id){
        return $this->comment_database->get_comment($comment_id);
    }

    /**
     * Summary of get_comments
     * @param mixed $comment_place_id
     * @param mixed $comment_place
     * @return array
     */
    public function get_comments($comment_place_id,$comment_place){
        if($comment_place == "Product"){
            return $this->comment_database->get_comments_using_product($comment_place_id);
        }
        else{
            if($comment_place == "Seller"){
                return $this->comment_database->get_comments_using_seller($comment_place_id);
            }
        }
        return [];
    }

    /**
     * Summary of delete_comment
     * @param mixed $comment_id
     * @return bool
     */
    public function delete_comment($comment_id = null){
        if(is_null($comment_id)){
            return $this->comment_database->delete_comment($this->comment_id);
        }
        return $this->comment_database->delete_comment($comment_id);
    }

}

?>