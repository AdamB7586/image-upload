<?php

namespace ImgUpload;

class ImageUpload implements UploadInterface{
    protected $errorNo;
    protected static $rootFolder;
    
    protected static $imageFolder = 'images'.DIRECTORY_SEPARATOR;
    protected static $thumbnailDir = 'thumbs'.DIRECTORY_SEPARATOR;
    
    public $maxFileSize = 9000000;
    public $imageSize = 0;
    public $allowedExt = array('gif', 'jpg', 'jpeg', 'png');
    public $minWidth = 200;
    public $minHeight = 150;
    
    public $createThumb = false;
    public $thumbWidth = 200;
    
    protected $imageInfo = false;

    /**
     * Constructor
     */
    public function __construct() {
        $this->setRootFolder(getcwd());
    }
    
    /**
     * Getter Will return one of the class variables based on the parameters
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->$name;
    }
    
    /**
     * Setter Will set the class variables based on the parameters given
     * @param string $name This should be the variable name
     * @param mixed $value This should be the value you wish to assign to the variable
     * @return $this
     */
    public function __set($name, $value) {
        if(isset($this->$name)) {
            $this->$name = $value;
        }
        return $this;
    }
    
    /**
     * Sets the minimum width and height that the uploaded images should be
     * @param int $width This should be the minimum width that an image should be
     * @param int $height This should be the minimum height that an image should be
     * @return $this
     */
    public function setMinWidthHeight($width, $height) {
        $this->setMinWidth($width);
        $this->setMinHeight($height);
        return $this;
    }
    
    /**
     * Sets the minimum width that the uploaded images should be
     * @param int $width This should be the minimum width that an image should be
     * @return $this
     */
    public function setMinWidth($width) {
        if(is_numeric($width)){
            $this->minWidth = intval($width);
        }
        return $this;
    }
    
    /**
     * Returns the minimum width that an image need to be in order to be uploaded
     * @return int This should be the minimum width in pixels
     */
    public function getMinWidth() {
        return $this->minWidth;
    }
    
    /**
     * Sets the minimum height that the uploaded images should be
     * @param int $height This should be the minimum width that an image should be
     * @return $this
     */
    public function setMinHeight($height) {
        if(is_numeric($height)){
            $this->minHeight = intval($height);
        }
        return $this;
    }
    
    /**
     * Returns the minimum height that an image need to be in order to be uploaded
     * @return int This should be the minimum height in pixels
     */
    public function getMinHeight() {
        return $this->minHeight;
    }
    
    /**
     * Sets the maximum image filesize that should be uploaded in bytes
     * @param int $bytes The should be the maximum filesize in bytes
     * @return $this
     */
    public function setMaxFileSize($bytes) {
        if(is_numeric($bytes)){
            $this->maxFileSize = intval($bytes);
        }
        return $this;
    }
    
    /**
     * Returns the maximum filesize that is allowed to be uploaded in bytes 
     * @return int The number of bytes will be returned
     */
    public function getMaxFileSize() {
        return $this->maxFileSize;
    }
    
    /**
     * Sets the root folder where the images folder can be located
     * @param string $folder This should be the file location where images are uploaded to
     */
    public function setRootFolder($folder) {
        if(is_string($folder)){
            self::$rootFolder = rtrim($folder, '\/').DIRECTORY_SEPARATOR;
        }
        return $this;
    }
    
    /**
     * Returns the root folder where all images will be uploaded to
     * @return string This will be the upload directory
     */
    public function getRootFolder() {
        return self::$rootFolder;
    }
    
    /**
     * Set the folder where the images will be uploaded to 
     * @param string $folder This should be the name of the folder that the main images will be uploaded to
     * @return $this
     */
    public function setImageFolder($folder) {
        if(is_string($folder)){
            self::$imageFolder = trim($folder, '\/').DIRECTORY_SEPARATOR;
        }
        return $this;
    }
    
    /**
     * Returns folder where the images will be uploaded
     * @return string Will return the folder where the main images will be uploaded
     */
    public function getImageFolder(){
        return self::$imageFolder;
    }
    
    /**
     * Sets the folder where any thumbnails will be uploaded to
     * @param string $folder This should be the name of the folder that the thumbnail images will be uploaded to
     * @return $this
     */
    public function setThumbFolder($folder) {
        if(is_string($folder)){
            self::$thumbnailDir = trim($folder, '\/').DIRECTORY_SEPARATOR;
        }
        return $this;
    }
    
    /**
     * Returns folder where the thumbnails will be uploaded
     * @return string Will return the folder where the thumbnails will be uploaded
     */
    public function getThumbFolder(){
        return self::$thumbnailDir;
    }
    
    /**
     * Upload an image to the server
     * @param array $image This should be the $_FILES['image']
     * @return boolean Returns true if image uploaded successfully else returns false
     */
    public function uploadImage($image) {
        if($this->checkFileName($image['name'])){
            $this->checkDirectoryExists($this->getRootFolder().$this->getImageFolder());
            if($this->isImageReal($image) && $this->imageExtCheck($image) && $this->imageSizeCheck($image) && $this->sizeGreaterThan($image) && !$this->imageExist($image)){
                if(move_uploaded_file($image['tmp_name'], $this->getRootFolder().$this->getImageFolder().basename($this->checkFileName($image['name'])))){
                    if($this->createThumb === true){$this->createImageThumb($image);}
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Create a thumbnail for the given image
     * @param array $image This should be the $_FILES['image']
     */
    public function createImageThumb($image) {
        $this->createCroppedImageThumb($image, 0, 0, $this->imageInfo['width'], $this->imageInfo['height']);
    }
    
    /**
     * Create a cropped image thumbnail for the given image based on given locations
     * @param array $image This should be the $_FILES['image']
     * @param int $x x-coordinate of start point
     * @param int $y y-coordinate of start point
     * @param int $w Source width
     * @param int $h Source height
     */
    public function createCroppedImageThumb($image, $x, $y, $w, $h){
        if($this->isImageReal($image) && $this->imageExtCheck($image) && $this->imageSizeCheck($image) && $this->sizeGreaterThan($image) && !$this->imageExist($image)){
            $new_height = intval($this->imageInfo['height'] * ($this->thumbWidth / $this->imageInfo['width']));
            if($this->imageInfo['type'] == 1) {
                $imgt = "ImageGIF";
                $imgcreatefrom = "ImageCreateFromGIF";
            }
            elseif($this->imageInfo['type'] == 2) {
                $imgt = "ImageJPEG";
                $imgcreatefrom = "ImageCreateFromJPEG";
            }
            elseif($this->imageInfo['type'] == 3) {
                $imgt = "ImagePNG";
                $imgcreatefrom = "ImageCreateFromPNG";
            }
            if($imgt) {
                $old_image = $imgcreatefrom($this->getRootFolder().$this->getImageFolder().basename($this->checkFileName($image['name'])));
                imagealphablending($old_image, true);
                $new_image = imagecreatetruecolor($this->thumbWidth, $new_height);
                imagecopyresampled($new_image, $old_image, 0, 0, $x, $y, $this->thumbWidth, $new_height, $w, $h);
                $imgt($new_image, $this->getRootFolder().$this->getImageFolder().$this->getThumbFolder().$this->checkFileName($image['name']));
            }
        }
    }
    
    /**
     * Delete and image from the server
     * @param string $image This should be the image name with extension
     * @return boolean Returns true if deleted else returns false
     */
    public function deleteImage($image) {
        if(file_exists($this->getRootFolder().$this->getImageFolder().$this->checkFileName($image["name"]))){
            unlink($this->getRootFolder().$this->getImageFolder().$this->checkFileName($image["name"]));
            unlink($this->getRootFolder().$this->getImageFolder().$this->getThumbFolder().$this->checkFileName($image["name"]));
            return true;
        }
        return false;
    }
    
    /**
     * Checks to see if the image is a real image
     * @param array $image This should be the $_FILES['image']
     * @return string|boolean If the image is real the mime type will be returned else will return false
     */
    protected function isImageReal($image) {
        list($this->imageInfo['width'], $this->imageInfo['height'], $this->imageInfo['type'], $this->imageInfo['attr']) = getimagesize($image["tmp_name"]);
        if($this->imageInfo !== false) {
           return $this->imageInfo['type'];
        }
        $this->errorNo = 1;
        return false;
    }
    
    /**
     * Checks to see if the image is within the allowed size limit
     * @param array $image This should be the $_FILES['image']
     * @return boolean Returns true if allowed size else returns false
     */
    protected function imageSizeCheck($image) {
        if($image['size'] > $this->maxFileSize) {
            $this->imageSize = $image['size'];
            return false;
        }
        $this->errorNo = 2;
        return true;
    }
    
    /**
     * Checks to see if the image has one of the allowed extensions
     * @param array $image This should be the $_FILES['image']
     * @return boolean Returns true if allowed else returns false
     */
    protected function imageExtCheck($image) {
        $fileType = strtolower(pathinfo($this->getRootFolder().$this->getImageFolder().$this->checkFileName($image['name']), PATHINFO_EXTENSION));
        if(in_array($fileType, $this->allowedExt)) {
            return true;
        }
        $this->errorNo = 3;
        return false;
    }
    
    /**
     * Checks to see if a image with the same name already exists on the server
     * @param array $image This should be the $_FILES['image']
     * @return boolean Returns true if image exists else return false
     */
    protected function imageExist($image) {
        if(file_exists($this->getRootFolder().$this->getImageFolder().basename($this->checkFileName($image["name"])))){
            $this->errorNo = 4;
            return true;
        }
        return false;
    }
    
    /**
     * Makes sure that the image dimensions are greater or equal to the minimum dimensions
     * @param array $image This should be the $_FILES['image']
     * @return boolean Returns true if the image dimensions are greater or equal else returns false
     */
    protected function sizeGreaterThan($image) {
        list($this->imageInfo['width'], $this->imageInfo['height'], $this->imageInfo['type'], $this->imageInfo['attr']) = getimagesize($image["tmp_name"]);
        if($this->imageInfo['width'] >= $this->minWidth && $this->imageInfo['height'] >= $this->minHeight){
            return true;
        }
        $this->errorNo = 5;
        return false;
    }
    
    /**
     * Checks to see if a directory exists if not it creates it
     * @param string $directory The location of the directory
     */
    protected function checkDirectoryExists($directory) {
        if(!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }
    
    /**
     * Returns the error message for image upload problems
     * @return string Returns the error message
     */
    public function getErrorMsg() {
        $errors = array(
            0 => 'An error occured while adding the image. Please try again!',
            1 => 'The image is not a valid image format',
            2 => 'The image is too large to upload please make sure your image is smaller than '. number_format(($this->maxFileSize / 100000), 2).'MB in size your image is '.$this->imageSize,
            3 => 'The image is not allowed! Please make sure your image has one of the allowed extensions',
            4 => 'The image with this name has already been uploaded or already exists on our server!',
            5 => 'The image dimensions are too small. It must be greater than 200px in width and 150px in height'
        );
        return $errors[intval($this->errorNo)];
    }
    
    /**
     * Remove any invalid characters from the filename
     * @param string $name This should be the original filename
     * @return string The filename will be returned with any invalid characters removed
     */
    protected function checkFileName($name){
        return preg_replace('/[^a-z0-9-_]/i', '', $name);
    }
}
