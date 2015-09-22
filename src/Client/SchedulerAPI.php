<?php

namespace JobScheduler\Client;

/**
 * SchedulerAPI provides helper functions to simplify process of communication between client and server.
 */
class SchedulerAPI
{
	private $host;
	
	/**
	 * Default constructor for SchedulerAPI.
	 *
	 * @param $host http address to directory where `status.php` and `new-job.php` scripts are located
	 */
	public function __construct($host)
	{
		if (substr($host, strlen($host)-1, 1) != '/') {
			$host += '/';
		}
		
		$this->host = $host;
	}
	
	/**
	 * Ask server to parse HTML page and download images (create new job on downloading HTML page and images).
	 *
	 * @param $remoteAddress address of html page which must be parsed.
	 *
	 * @return if successful, then result is JSON response about placing job to queue; otherwise FALSE.
	 * @see GetJobStatus()
	 */
	public function AskForDownloading($remoteAddress)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $this->host . "new-job.php?htmlpage=" . urlencode($remoteAddress));
		$response = curl_exec($curl);
		
		$status = json_decode($response);
		
		if ($status === NULL) {
			return FALSE;
		}
		
		return $status;
	}
	
	/**
	 * Get current status of job with id = $jobId. If job is completed, then it returns job's results too.
	 *
	 * @param $jobId unique job id from AskForDownloading() function (contained in JSON response).
	 *
	 * @return if successful, then result is JSON response about job's status; otherwise FALSE.
	 * @see AskForDownloading()
	 */
	public function GetJobStatus($jobId)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $this->host . "status.php?job_id=" . urlencode($jobId));
		$response = curl_exec($curl);
		
		$status = json_decode($response);
		
		if ($status === NULL) {
			return FALSE;
		}
		
		return $status;
	}
}
