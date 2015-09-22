<?php

use JobScheduler\Service\PageParser;

class PageParserTest extends \PHPUnit_Framework_TestCase
{	
  private	$pageParser;
	private $htmlContent;
	
	public function setUp()
	{
  	$this->pageParser = new PageParser();
		$this->htmlContent = file_get_contents("test/fixtures/rabbitmq.htm");
	}
	
  public function testGetAllImagesAddresses()
  {
  	$desiredImages = array(
  		"/img/rabbitmq_logo_strap.png",	
  		"/img/logo-pivotal-118x25.png",
  		"/img/tutorials/python-one.png",
  		"/img/tutorials/python-two.png",
  		"/img/tutorials/python-three.png",
  		"/img/tutorials/python-four.png",
  		"/img/tutorials/python-five.png",
  		"/img/tutorials/python-six.png",
  		"/img/tutorials/prefetch-count.png",
  	);
  	
  	$parsedImages = $this->pageParser->getAllImagesAddresses($this->htmlContent);
  	
  	$this->assertEquals(array(), array_diff($desiredImages, $parsedImages));
  }
  
  public function testNormalizeImagesPaths()
  {
  	$parsedImages = array(
  		"../img/rabbitmq_logo_strap.png",	
  		"./img/logo-pivotal-118x25.png",
  		"/img/tutorials/python-one.png",
  		"/img/tutorials/python-two.png",
  		"/img/tutorials/python-three.png",
  		"/img/tutorials/python-four.png",
  		"/img/tutorials/python-five.png",
  		"/img/tutorials/python-six.png",
  		"/img/tutorials/prefetch-count.png",
  	);
  	
  	$desiredImages = array(
  		"http://www.rabbitmq.com/tutorials/../img/rabbitmq_logo_strap.png",	
  		"http://www.rabbitmq.com/img/logo-pivotal-118x25.png",
  		"http://www.rabbitmq.com/img/tutorials/python-one.png",
  		"http://www.rabbitmq.com/img/tutorials/python-two.png",
  		"http://www.rabbitmq.com/img/tutorials/python-three.png",
  		"http://www.rabbitmq.com/img/tutorials/python-four.png",
  		"http://www.rabbitmq.com/img/tutorials/python-five.png",
  		"http://www.rabbitmq.com/img/tutorials/python-six.png",
  		"http://www.rabbitmq.com/img/tutorials/prefetch-count.png",
  	);
  	
  	$normalizedImages = $this->pageParser->normalizeImagesPaths("http://www.rabbitmq.com/tutorials/tutorial-two-python.html", $parsedImages);
  	
  	$this->assertEquals(array(), array_diff($desiredImages, $normalizedImages));
  }
}
