<?php

class JobRepositoryTest extends \PHPUnit_Framework_TestCase
{
	private $entityManager;
	
	public function setUp()
	{
		global $entityManager;
		
		$this->entityManager = $entityManager;
	}
	
  public function testCreateJob()
  {  	
  	$repository = $this->entityManager->getRepository("JobScheduler\Entity\Job");
  	$job = $repository->createJob("random-page.com");
  	
  	if ($job === NULL) {
  		$this->fail("Failed to create Job instance");
  	}
  
    $this->assertEquals("random-page.com", $job->getHtmlPage());
  }
}
