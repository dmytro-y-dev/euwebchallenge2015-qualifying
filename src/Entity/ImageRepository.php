<?php

namespace JobScheduler\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
	/**
	 * Create array of new images and return to caller.
	 *
	 * @param $job job which contains images for downloading.
	 * @param $remoteAddresses Internet addresses where images are located.
	 * 
	 * @return array with new Image instances.
	 */
	public function createImages(Job $job, $remoteAddresses)
	{
		$images = array();
		
		foreach ($remoteAddresses as $address) {
			$images[] = new Image($job, $address);
		}
		
		return $images;
	}
}

