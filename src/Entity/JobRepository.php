<?php

namespace JobScheduler\Entity;

use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
	/**
	 * Create new job and return to caller.
	 *
	 * @param $htmlpage associated with job remote HTML page address.
	 * 
	 * @return new job instance.
	 */
	public function createJob($htmlpage)
	{
		$job = new Job($htmlpage);
		
		return $job;
	}
}
