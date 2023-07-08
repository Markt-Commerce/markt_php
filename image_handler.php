<?php

namespace Markt;

include_once "dbconnections/product_images_database_connections.php";
include_once "definitions.php";

use Markt\DB\ProductImages;

/**
 * Class for handling Images sent to the server and Images 
 * gotten from the server. Sends the Image to an image bucket 
 * and validates the image
 */
class ImageHandler{

    /**
     * valid extensions
     */
    private $allowed_extensions = array('jpg','png','webp');

    /**
     * available valid mimes
     */
    private $allowed_mimes = array('image/jpeg','image/png','image/webp');

    /**
     * Constructor for the ImageHandler Class
     */
    public function __construct() {
    }

    /**
     * check if the extension is among the valid extensions.
     * Valid extensions are `.jpg`, `.png`, `.webp`
     * @param mixed $file
     * @return bool
     */
    private function is_valid_extension($file){
        $file_extension = pathinfo(UPLOAD_DIRECTORY.$file["name"],PATHINFO_EXTENSION);
        return in_array($file_extension,$this->allowed_extensions);
    }

    /**
     * Checks if the file size is in the correct range
     * the file size should not be more than 5mb
     * @param mixed $file
     * @return bool
     */
    private function is_valid_size($file){
        $filesize = $file["size"];
        return $filesize <= MAXSIZE && $filesize > 0;
    }

    /**
     * check if the mime type is valid and is among the accepted
     * mimes
     * @param array $file
     * @return bool
     */
    private function is_valid_mime_type($file){
        $info = mime_content_type($file["tmp_name"]);
        return in_array($info,$this->allowed_mimes);
    }

    /**
     * Summary of validate
     * @param array $file
     * @return bool
     */
    private function validate($file){
        return $this->is_valid_extension($file) && $this->is_valid_mime_type($file) && $this->is_valid_size($file);
    }

    /**
     * Summary of compress
     * @param array $file
     * @param int $quality
     * @return boolean
     */
    private function compress($file,$image_name,$quality){
        $info = getimagesize($file["tmp_name"]);

        if ($info["mime"] == "image/jpeg") {
            $image = imagecreatefromjpeg($file["tmp_name"]);
        } else {
            if ($info["mime"] == "image/png") {
            $image = imagecreatefrompng($file["tmp_name"]);
            }
        }
        
        return imagejpeg($image,"uploads/".$image_name,$quality);
    }

    /**
     * uploads an image to the server checking if the image passes all 
     * specifications. Returns the given name of the image in the server if successful
     * and null if not
     * @param array $Image The image to be uploaded. Gotten from the $_FILES array
     * @return string
     */
    public function upload_image($Image){
        $image_name_split = explode(".",uniqid("image-",true));
        $image_name = "";
        for ($i=0; $i < count($image_name_split); $i++) { 
            $image_name = $image_name.$image_name_split[$i];
        }
        //NOTE: We need to check the extension of the file to avoid creating the work image 
        //extension i.e creating a png file for a jpeg image upload
        if($this->validate($Image)){
            $mime_type= mime_content_type($Image["tmp_name"]);
            if($mime_type == 'image/jpeg'){
                $image_name_large = $image_name."large.jpg";
                $image_name_medium = $image_name."medium.jpg";
                $image_name_small = $image_name."small.jpg";
            }
            else{
                if($mime_type == 'image/png'){
                    $image_name_large = $image_name."large.jpg";
                    $image_name_medium = $image_name."medium.jpg";
                    $image_name_small = $image_name."small.jpg";
                }
                else{
                    $image_name_large = $image_name."large.jpg";
                    $image_name_medium = $image_name."medium.jpg";
                    $image_name_small = $image_name."small.jpg";
                }
            }
            $this->compress($Image,$image_name_large,60);
            $this->compress($Image,$image_name_medium,40);
            $this->compress($Image,$image_name_small,20);
            return $image_name;
        }
        return "";
    }

    /**
     * uploads a `chat` image to the server checking if the image passes all 
     * specifications. Returns the given name of the image in the server if successful
     * and null if not
     * @param array $Image The image to be uploaded. Gotten from the $_FILES array
     * @return string
     */
    public function upload_chat_image($Image){
        if(!empty($Image) && is_array($Image) && isset($Image["tmp_name"])){
            $image_name_split = explode(".",uniqid("chatimage-",true));
            $image_name = "";
            for ($i=0; $i < count($image_name_split); $i++) { 
                $image_name = $image_name.$image_name_split[$i];
            }
            //NOTE: We need to check the extension of the file to avoid creating the work image 
            //extension i.e creating a png file for a jpeg image upload
            $mime_type = mime_content_type($Image["tmp_name"]);
            if($mime_type == 'image/jpeg'){
                $image_name = $image_name.".jpg";
            }
            else{
                if($mime_type == 'image/png'){
                    $image_name = $image_name.".jpg";
                }
                else{
                    $image_name = $image_name.".jpg";
                }
            }
            if($this->validate($Image)){
                $this->compress($Image,$image_name,40);
                return $image_name;
            }
        }
        return "";
    }

    /**
     * uploads images in an array to the server and returns an
     * array containing the names given to the images
     * @param array $ImageList
     */
    public function upload_multiple_images($ImageList){
        $uploaded_imagenames_array = array();
        foreach($ImageList as $Image){
            $uploaded_imagenames_array[count($uploaded_imagenames_array)] = $this->upload_image($Image);
        }
        return $uploaded_imagenames_array;
    }
    
    /**
     * deletes chat_image from the database and the filesystem
     * returns `true` if successful and `false` if unsuccessful
     * @param string $image_id
     * @return bool
     */
    public function delete_chat_image($image_id){
        return unlink(UPLOAD_DIRECTORY.$image_id.".jpg");
    }

    /**
     * deletes image from the database and the filesystem
     * returns `true` if successful and `false` if unsuccessful
     * @param string $image_id
     * @return bool
     */
    public function delete_product_image($image_id){
        $unlinkall = unlink(UPLOAD_DIRECTORY.$image_id."large.jpg") && unlink(UPLOAD_DIRECTORY.$image_id."medium.jpg") && unlink(UPLOAD_DIRECTORY.$image_id."small.jpg");
        return $unlinkall;
    }

    /**
     * deletes user profile images from the database.
     * returns true if successful and false if unsuccessful
     * @param string $image_name
     * @return bool
     */
    public function delete_user_image($image_name){
        if(!empty($image_name)){
            $large_deleted = unlink(UPLOAD_DIRECTORY.$image_name."large");
            $medium_deleted = unlink(UPLOAD_DIRECTORY.$image_name."medium");
            $small_deleted = unlink(UPLOAD_DIRECTORY.$image_name."small");
            return $large_deleted && $medium_deleted && $small_deleted;
        }
        else{
            return false;
        }
    }

}

?>