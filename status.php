<?php

/**
 * Server script which returns status of job.
 *
 * Input parameters:
 * $_GET['job_id'] - unique id of job for which you want to get status.
 * 
 * Output:
 * 1. If job with id = `job_id` exists, then the script returns JSON with job's status.
 * 2. If job with id = `job_id` doesn't exist or `job_id` is not specified in $_GET, then the script returns JSON with information about error.
 *
 * Error JSON format:
 *
 *   array(
 *	   'status' => 'error',
 *	   'hint' => 'Description of error'
 *   )
 *
 * Status JSON format:
 *
 *   array(
 *     'job' => array(
 *			 'id' => 'Job id | integer',
 *			 'status' => 'Job status (one of the next: `pending`, `downloading-html`, `parsing-html`, `downloading-images`, `failed`, `completed`) | string',
 *			 'htmlpage' => 'HTML page with images to download | string',
 *       'started_on' => 'Date and time when job was started | JSONed DateTime object',
 *       'completed_on' => 'Date and time when job was completed | JSONed DateTime object'
 *		 ),
 *     'images' => array(
 *       array(
 *         'remote_address' => 'URL of the image | string',
 *         'status' => 'Image status (one of the next: `pending`, `downloading`, `bad-image-format`, `failed`, `downloaded`) | string',
 *         'local_address' => 'Address of the image on file system | string',
 *         'height' => 'Height of the image | integer',
 *         'width' => 'Width of the image | integer',
 *         'size' => 'Size of image file | integer',
 *         'content_type' => 'Content type of the image | string',
 *		   ),
 *       ...
 *		 )
 *   )
 */

require_once 'src/bootstrap.php';

if (!isset($_GET['job_id'])) {
	exit(json_encode(array(
		'status' => 'error',
		'hint' => "Error: Required argument `job_id` is not specified.",
	)));
}

$jobId = (int)$_GET['job_id'];
$job = $entityManager->getRepository('JobScheduler\Entity\Job')->findOneById($jobId);

if ($job === NULL) {
	exit(json_encode(array(
		'status' => 'error',
		'hint' => "Error: Unable to get requested Job from repository.",
	)));
}

// Get actual information about Job

$result = array(
	'job' => array(
		'id' => $job->getId(),
		'status' => $job->getStatus(),
		'htmlpage' => $job->getHtmlPage(),
	)
);

if ($job->getStatus() != 'pending') {
	$result['job']['started_on'] = $job->getDateStarted();
}

if ($job->getStatus() == 'completed') {
	$result['job']['completed_on'] = $job->getDateFinished();
}

// Get actual information about job's images 

if (in_array($job->getStatus(), array('completed', 'downloading-images'))) {
	$result['images'] = array();
	
	foreach ($job->getImages() as $image) {
		$podImage = array(
			'remote_address' => $image->getRemoteAddress(),
			'status' => $image->getStatus(),
		);
		
		if ($image->getStatus() == "downloaded") {
			$podImage['local_address'] = $image->getLocalAddress();
			$podImage['height'] = $image->getHeight();
			$podImage['width'] = $image->getWidth();
			$podImage['size'] = $image->getSize();
			$podImage['content_type'] = $image->getContentType();
		}
		
		$result['images'][] = $podImage;
	}
}

// Output result in JSON

echo json_encode($result);
