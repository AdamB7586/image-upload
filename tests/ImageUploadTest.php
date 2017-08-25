<?php
namespace ImgUpload\Tests;

use ImgUpload\ImageUpload;
use PHPUnit\Framework\TestCase;

class ImageUploadTest extends TestCase{
    
    protected $upload;
    
    public function setUp(){
        $this->upload = new ImageUpload();
    }
    
    public function tearDown(){
        unset($this->upload);
    }
    
    public function exampleTest(){
        $this->markTestIncomplete('This test has not yet been implemented');
    }
}
