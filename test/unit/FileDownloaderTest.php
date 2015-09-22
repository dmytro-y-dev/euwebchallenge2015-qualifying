<?php

use JobScheduler\Service\FileDownloader;

class FileDownloaderTest extends \PHPUnit_Framework_TestCase
{	
  private	$fileDownloader;
	private $desiredHtmlContent;
	private $desiredImageContent;
	
	public function setUp()
	{
  	$this->fileDownloader = new FileDownloader();
		$this->desiredHtmlContent = file_get_contents("test/fixtures/rabbitmq.htm");
		$this->desiredImageContent = file_get_contents("test/fixtures/python-two.png");
	}
	
  public function testGetRemoteHTMLFileContent()
  {  	
  	$htmlContent = $this->fileDownloader->getRemoteHTMLFileContent("http://www.rabbitmq.com/tutorials/tutorial-two-python.html");
  	
  	$this->assertEquals($this->desiredHtmlContent, $htmlContent);
  }
	
  public function testGetRemoteImage()
  {  	
  	$imageContent = $this->fileDownloader->getRemoteImage("http://www.rabbitmq.com/img/tutorials/python-two.png");
  	
  	$this->assertEquals($this->desiredImageContent, $imageContent);
  }
}
