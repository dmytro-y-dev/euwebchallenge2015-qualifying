<?php

require_once __DIR__.'/../src/Client/SchedulerAPI.php';

// Specify input data aka address of desired html page

$remoteAddress = "http://askubuntu.com/questions/299870/http-post-and-get-using-curl-in-linux";

// Create instance of SchedulerAPI and send request for downloading $remoteAddress

$schedulerAPI = new \JobScheduler\Client\SchedulerAPI("http://localhost/");
$schedulerNewJob = $schedulerAPI->AskForDownloading($remoteAddress);

if (!$schedulerNewJob || $schedulerNewJob->status == 'error') {
	$errorHint = "Unable to create job for address `{$remoteAddress}`.\n";
	
	if ($schedulerNewJob) {
		$errorHint .= $schedulerNewJob->hint . "\n";
	}
	
	exit($errorHint);
}

// Monitor job's status until it is completed or failed

$jobId = $schedulerNewJob->job_id;

do {
	$jobStatus = $schedulerAPI->GetJobStatus($jobId);
	usleep(1000);
} while ($jobStatus && !in_array($jobStatus->job->status, array('completed', 'failed')));

// Output results

echo "Work result:\n";
var_dump($jobStatus);
