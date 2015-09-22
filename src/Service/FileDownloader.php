<?php

namespace JobScheduler\Service;

/**
 * This class provides functions to download HTML pages and images from Internet.
 */
class FileDownloader
{
	/**
	 * Download HTML from Internet and get its content.
	 *
	 * @param $remoteAddress URL of HTML file.
	 *
	 * @return if successful, then content of the file; otherwise FALSE.
	 */
	public function getRemoteHTMLFileContent($remoteAddress)
	{
		$htmlFileContent = @file_get_contents($remoteAddress);
	
		return $htmlFileContent;
	}
	
	/**
	 * Download image from Internet and get its content.
	 *
	 * @param $remoteAddress image file.
	 *
	 * @return if successful, then content of the file; otherwise FALSE.
	 */
	public function getRemoteImage($remoteAddress)
	{	
		$htmlFileContent = @file_get_contents($remoteAddress);
	
		return $htmlFileContent;
	}
}
