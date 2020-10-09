<?php
namespace ImgUpload\Tests;

use ImgUpload\ImageUpload;
use PHPUnit\Framework\TestCase;

class ImageUploadTest extends TestCase
{
    
    protected $upload;
    
    protected function setUp(): void
    {
        $this->upload = new ImageUpload();
    }
    
    protected function tearDown(): void
    {
        $this->upload = null;
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::__get
     * @covers ImgUpload\ImageUpload::__set
     */
    public function testGettersSetters()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::setRootFolder
     * @covers ImgUpload\ImageUpload::setMinWidthHeight
     * @covers ImgUpload\ImageUpload::setMinWidth
     * @covers ImgUpload\ImageUpload::getMinWidth
     * @covers ImgUpload\ImageUpload::setMinHeight
     * @covers ImgUpload\ImageUpload::getMinHeight
     */
    public function testSetMinWidthHeight()
    {
        $this->assertEquals(400, $this->upload->getMinWidth());
        $this->assertEquals(300, $this->upload->getMinHeight());
        $this->assertObjectHasAttribute('minWidth', $this->upload->setMinWidthHeight(500, false));
        $this->assertEquals(500, $this->upload->getMinWidth());
        $this->assertEquals(300, $this->upload->getMinHeight());
        $this->assertObjectHasAttribute('minWidth', $this->upload->setMinWidthHeight('Hello', 'World'));
        $this->assertEquals(500, $this->upload->getMinWidth());
        $this->assertEquals(300, $this->upload->getMinHeight());
        $this->assertObjectHasAttribute('minWidth', $this->upload->setMinWidthHeight(false, 200));
        $this->assertEquals(500, $this->upload->getMinWidth());
        $this->assertEquals(200, $this->upload->getMinHeight());
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::setRootFolder
     * @covers ImgUpload\ImageUpload::setMaxFileSize
     * @covers ImgUpload\ImageUpload::getMaxFileSize
     */
    public function testMaxFileSize()
    {
        $this->assertEquals(20000000, $this->upload->getMaxFileSize());
        $this->assertObjectHasAttribute('maxFileSize', $this->upload->setMaxFileSize('8000000'));
        $this->assertEquals(8000000, $this->upload->getMaxFileSize());
        $this->assertObjectHasAttribute('maxFileSize', $this->upload->setMaxFileSize('Hello'));
        $this->assertEquals(8000000, $this->upload->getMaxFileSize());
        $this->assertObjectHasAttribute('maxFileSize', $this->upload->setMaxFileSize(false));
        $this->assertEquals(8000000, $this->upload->getMaxFileSize());
        $this->assertObjectHasAttribute('maxFileSize', $this->upload->setMaxFileSize(9000000));
        $this->assertEquals(9000000, $this->upload->maxFileSize);
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::setRootFolder
     * @covers ImgUpload\ImageUpload::getRootFolder
     */
    public function testRootFolder()
    {
        $this->assertObjectHasAttribute('imageSize', $this->upload->setRootFolder(dirname(__FILE__).DIRECTORY_SEPARATOR.'uploads'));
        $this->assertEquals(dirname(__FILE__).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR, $this->upload->getRootFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setRootFolder(1615515));
        $this->assertEquals(dirname(__FILE__).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR, $this->upload->getRootFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setRootFolder(true));
        $this->assertEquals(dirname(__FILE__).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR, $this->upload->getRootFolder());
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::setRootFolder
     * @covers ImgUpload\ImageUpload::setImageFolder
     * @covers ImgUpload\ImageUpload::getImageFolder
     */
    public function testImageFolder()
    {
        $this->assertEquals('images'.DIRECTORY_SEPARATOR, $this->upload->getImageFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setImageFolder(false));
        $this->assertEquals('images'.DIRECTORY_SEPARATOR, $this->upload->getImageFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setImageFolder(15464));
        $this->assertEquals('images'.DIRECTORY_SEPARATOR, $this->upload->getImageFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setImageFolder('hello-world'));
        $this->assertEquals('hello-world'.DIRECTORY_SEPARATOR, $this->upload->getImageFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setImageFolder('images/'));
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::setRootFolder
     * @covers ImgUpload\ImageUpload::setThumbFolder
     * @covers ImgUpload\ImageUpload::getThumbFolder
     */
    public function testThumbFolder()
    {
        $this->assertEquals('thumbs'.DIRECTORY_SEPARATOR, $this->upload->getThumbFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setThumbFolder(false));
        $this->assertEquals('thumbs'.DIRECTORY_SEPARATOR, $this->upload->getThumbFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setThumbFolder(15464));
        $this->assertEquals('thumbs'.DIRECTORY_SEPARATOR, $this->upload->getThumbFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setThumbFolder('hello-world'));
        $this->assertEquals('hello-world'.DIRECTORY_SEPARATOR, $this->upload->getThumbFolder());
        $this->assertObjectHasAttribute('imageSize', $this->upload->setThumbFolder('thumbs/'));
    }
    
    /**
     * @covers ImgUpload\ImageUpload::__construct
     * @covers ImgUpload\ImageUpload::setRootFolder
     * @covers ImgUpload\ImageUpload::uploadImage
     * @covers ImgUpload\ImageUpload::checkFileName
     * @covers ImgUpload\ImageUpload::checkDirectoryExists
     * @covers ImgUpload\ImageUpload::getRootFolder
     * @covers ImgUpload\ImageUpload::getImageFolder
     * @covers ImgUpload\ImageUpload::isImageReal
     * @covers ImgUpload\ImageUpload::imageExtCheck
     * @covers ImgUpload\ImageUpload::imageSizeCheck
     * @covers ImgUpload\ImageUpload::sizeGreaterThan
     * @covers ImgUpload\ImageUpload::imageExist
     * @covers ImgUpload\ImageUpload::createImageThumb
     * @covers ImgUpload\ImageUpload::createCroppedImageThumb
     * @covers ImgUpload\ImageUpload::getThumbFolder
     * @covers ImgUpload\ImageUpload::getErrorMsg
     */
    public function testImageUpload()
    {
        $this->markTestIncomplete('This test has not yet been implemented');
    }
}
