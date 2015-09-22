<?php

/**
 * Server script that does actual job on parsing HTML files and downloading images.
 *
 * Input parameters:
 * $message from RabbitMQ queue.
 * 
 * Output:
 * 1. Side Effects: Worker script updates job's and images' status on every significant actions, so `status.php` script can catch changes.
 * 2. If error is obtained, then the script outputs its details to console window.
 */

require_once 'src/bootstrap.php';

use PhpAmqpLib\Connection\AMQPConnection;

use JobScheduler\Service\ImageStorage;
use JobScheduler\Service\PageParser;
use JobScheduler\Service\FileDownloader;

/**
 * Update `status` field of entity and send changes to RDBMS.
 * 
 * @param $status new status message.
 * @param $entity entity which should update status (Image or Job).
 * @param $entityManager doctrine's entity manager
 */
function UpdateEntityStatus($status, $entity, $entityManager)
{
	$entity->setStatus($status);
	$entityManager->flush();
}
	
/**
 * Get job by unique id, download and parse its assigned HTML file, then download and save
 * images on local file system. Update job's status on any significant change.
 * 
 * @param $message RabbitMQ message with job's id.
 */
function WorkerProcessJob(\PhpAmqpLib\Message\AMQPMessage $message)
{
	global $entityManager;
	
	$jobId = (int)$message->body;

	$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
	
	// Get assigned Job from repository
	
	$job = $entityManager->getRepository('JobScheduler\Entity\Job')->findOneById($jobId);
	
	if ($job === NULL) {
		echo "Error: Unable to get requested Job from repository.\n";
		
		return;
	}
	
	if ($job->getStatus() == 'failed') {
		// Just removing message from queue and skipping, because previous
		// attempt failed for some reason (let's don't try doing it again until
		// user asks)
		return;
	}
	
	echo "Starting to do job with id = $jobId\n";
	
	$job->setDateStarted(new \DateTime());
	
	$imageStorage   = new ImageStorage("storage");
	$fileDownloader = new FileDownloader();
	$pageParser     = new PageParser();
	
	// Download requested HTML file
	
	UpdateEntityStatus("downloading-html", $job, $entityManager);
	
	$htmlPageContent = $fileDownloader->getRemoteHTMLFileContent($job->getHtmlPage());
	
	if ($htmlPageContent === FALSE) {
		UpdateEntityStatus("failed", $job, $entityManager);
		
		echo "Error: Unable to download requested page `{$job->getHtmlPage()}`.\n";
		
		return;
	}
	
	// Parse obtained HTML file
	
	UpdateEntityStatus("parsing-html", $job, $entityManager);
	
	$parsedImagesAddresses = $pageParser->getAllImagesAddresses($htmlPageContent);
	
	if ($parsedImagesAddresses === FALSE) {
		UpdateEntityStatus("failed", $job, $entityManager);
		
		echo "Error: Bad HTML file on address `{$job->getHtmlPage()}`.\n";
		
		return;
	}
	
	// Normalize paths of images from HTML file
	
	$normalizedImagesAddresses = $pageParser->normalizeImagesPaths($job->getHtmlPage(), $parsedImagesAddresses);
	
	if ($normalizedImagesAddresses === FALSE) {
		UpdateEntityStatus("failed", $job, $entityManager);
		
		echo "Internal Error: Bad remote page address format `{$job->getHtmlPage()}`.\n";
		
		return;
	}
	
	// Save remote paths of images to DB, so we can watch status of downloading
	
	$images = $entityManager->getRepository('JobScheduler\Entity\Image')->createImages($job, $normalizedImagesAddresses);
	
	foreach ($images as $image) {
		$image->setStatus("pending");
		
		$entityManager->persist($image);
	}
	
	$entityManager->flush();
	
	// Download images
	
	UpdateEntityStatus("downloading-images", $job, $entityManager);
	
	foreach ($images as $image) {
		UpdateEntityStatus("downloading", $image, $entityManager);
		
		$imageContent = $fileDownloader->getRemoteImage($image->getRemoteAddress());
		
		if ($imageContent === FALSE) {
			UpdateEntityStatus("failed", $image, $entityManager);
		} else {
			$localFilePath = $imageStorage->saveImageToStorage($job->getId(), $imageContent);
			
			if ($localFilePath === FALSE) {
				UpdateEntityStatus("bad-image-format", $image, $entityManager);
			} else {
				$imageInfo = $imageStorage->getImageInfo($imageContent);
				
				$image->setLocalAddress($localFilePath);
				$image->setContentType($imageInfo['content-type']);
				$image->setWidth($imageInfo['width']);
				$image->setHeight($imageInfo['height']);
				$image->setSize($imageInfo['size']);
				$image->setStatus("downloaded");
	
				$entityManager->flush();
			}
		}
	}
	
	$job->setDateFinished(new \DateTime());
	UpdateEntityStatus("completed", $job, $entityManager);
	
	echo "Job $jobId is completed\n";
	echo "Waiting for work to do...\n";
}

/**
 * Callback on worker's termination.
 * 
 * @param $ch RabbitMQ current channel.
 * @param $conn RabbitMQ current connection.
 */
function WorkerShutdown(\PhpAmqpLib\Channel\AMQPChannel $ch, \PhpAmqpLib\Connection\AbstractConnection $conn)
{
	echo "Stopping worker...\n";
  
  $ch->close();
  $conn->close();
}

echo "Starting worker...\n";

$exchange = 'router';
$queue = 'jobs_scheduler';
$consumer_tag = 'consumer';

$conn = new AMQPConnection($config['rabbitmq']['host'],
	$config['rabbitmq']['port'],
	$config['rabbitmq']['user'],
	$config['rabbitmq']['pass'],
	$config['rabbitmq']['vhost']
);
$ch = $conn->channel();

$ch->queue_declare($queue, false, true, false, false);
$ch->exchange_declare($exchange, 'direct', false, true, false);
$ch->queue_bind($queue, $exchange);

$ch->basic_consume($queue, $consumer_tag, false, false, false, false, 'WorkerProcessJob');

register_shutdown_function('WorkerShutdown', $ch, $conn);

echo "Waiting for work to do...\n";

while (count($ch->callbacks)) {
  $ch->wait();
}
