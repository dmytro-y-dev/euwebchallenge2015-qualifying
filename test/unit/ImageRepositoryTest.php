<?php

use JobScheduler\Entity\Image;
use JobScheduler\Entity\Job;

class ImageRepositoryTest extends \PHPUnit_Framework_TestCase
{
	private $entityManager;
	
	public function setUp()
	{
		global $entityManager;
		
		$this->entityManager = $entityManager;
	}
	
  public function testCreateImages()
  {
  	$addresses = array(
  		"test.com/kitten1.jpg",
  		"test.com/kitten2.jpg",
  		"test.com/kitten3.jpg",
  	);
  	$job = new Job("some-url");
  	
  	$repository = $this->entityManager->getRepository("JobScheduler\Entity\Image");
  	$images = $repository->createImages($job, $addresses);
  	
  	$this->assertEquals(3, count($images));
  	
  	foreach ($images as $image) {
  		$this->assertEquals(true, $image instanceof Image);
  		$this->assertEquals(true, in_array($image->getRemoteAddress(), $addresses));
  		
  		$key = array_search($image->getRemoteAddress(), $addresses);
  		unset($addresses[$key]);
  	}
  }
}
