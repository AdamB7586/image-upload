<?php
namespace ImgUpload;

interface UploadInterface{
    public function uploadImage($image);
    public function deleteImage($image);
}