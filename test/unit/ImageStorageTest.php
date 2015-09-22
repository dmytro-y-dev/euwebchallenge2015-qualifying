<?php

use JobScheduler\Service\ImageStorage;

class ImageStorageTest extends \PHPUnit_Framework_TestCase
{	
  private	$jobId;
  private	$remoteAddress;
  private	$desiredLocalAddress;
	private $imageContent;
	private $imageStorage;
	
	public function setUp()
	{
  	$this->jobId = 1;
  	$this->remoteAddress = "http://www.rabbitmq.com/img/tutorials/python-two.png";
  	$this->desiredLocalAddress = "test-tmp/1/1.png";
		$this->imageContent = file_get_contents("test/fixtures/python-two.png");
		$this->imageStorage = new ImageStorage("test-tmp");
		
		@mkdir("test-tmp");
	}
	
	public function tearDown()
	{
		@unlink("test-tmp/1/1.png");
		@rmdir("test-tmp/1");
		@rmdir("test-tmp");
	}
	
	public function testGetImageInfo()
  {
  	$imageInfo = $this->imageStorage->getImageInfo($this->imageContent);
  	
  	$this->assertEquals("image/png", $imageInfo['content-type']);
  	$this->assertEquals(332, $imageInfo['width']);
  	$this->assertEquals(111, $imageInfo['height']);
  	$this->assertEquals(7524, $imageInfo['size']);
  }
  
	public function testGetImageInfoBadContent()
  {
  	$result = $this->imageStorage->getImageInfo("foobar");
  	
  	$this->assertEquals(FALSE, $result);
  }
	
  public function testSaveImageToStorage()
  {
  	$localFileAddress = $this->imageStorage->saveImageToStorage($this->jobId, $this->imageContent);
  	
  	$this->assertEquals($this->desiredLocalAddress, $localFileAddress);
  	$this->assertEquals(true, file_exists($localFileAddress));
  	$this->assertEquals(true, file_get_contents($localFileAddress) == $this->imageContent);
  }
  
  public function testSaveImageToStorageBadContentType()
  {
  	$localFileAddress = $this->imageStorage->saveImageToStorage($this->jobId, "foobar");
  	
  	$this->assertEquals(FALSE, $localFileAddress);
  }
}
