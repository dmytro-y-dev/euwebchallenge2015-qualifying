<?php

/**
 * Server script which accepts new jobs and sends message for workers about new job through RabbitMQ queue.
 *
 * Input parameters:
 * $_GET['htmlpage'] - address to HTML page which must be parsed. It must match the next pattern: "http://site-name/".
 * 
 * Output:
 * 1. If `htmlpage` parameter has good format (something like `http://site.com/`), then the script returns JSON with job's id.
 * 2. If `htmlpage` parameter has bad format (something like `badformat.com`), then the script returns JSON with information about error.
 *
 * Error JSON format:
 *
 *   array(
 *	   'status' => 'error',
 *	   'hint' => 'Description of error'
 *   )
 *
 * JSON with job's id format:
 *
 *   array(
 *	   'status' => 'accepted',
 *	   'job_id' => 'Job's id | integer'
 *   )
 */

require_once 'src/bootstrap.php';

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

if (!isset($_GET['htmlpage'])) {
	exit(json_encode(array(
		'status' => 'error',
		'hint' => "Error: Required argument `htmlpage` is not specified.",
	)));
}

if (substr_count($_GET['htmlpage'], "/") == 2) {
	$_GET['htmlpage'] .= '/';
}

if (preg_match("#(http://)(.)+/(.)*#", $_GET['htmlpage']) != 1) {
	exit(json_encode(array(
		'status' => 'error',
		'hint' => "Error: Bad `htmlpage` format. It should be something like `http://site.com/pg`",
	)));
}

$remoteAddress = $_GET['htmlpage'];

// Create new job

$job = $entityManager->getRepository('JobScheduler\Entity\Job')->createJob($remoteAddress);
$job->setStatus("pending");
$entityManager->persist($job);
$entityManager->flush();

// Give new job to one of workers

$exchange = 'router';
$queue = 'jobs_scheduler';

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

$msg = new AMQPMessage($job->getId(), array('content_type' => 'text/plain', 'delivery_mode' => 2));
$ch->basic_publish($msg, $exchange);

$ch->close();
$conn->close();

echo json_encode(array(
	'status' => 'accepted',
	'job_id' => $job->getId(),
));
