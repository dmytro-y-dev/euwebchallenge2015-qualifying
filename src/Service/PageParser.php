<?php

namespace JobScheduler\Service;

/**
 * This class enables user to parse HTML file and get paths to all images from it.
 */
class PageParser
{
	/**
	 * Parse HTML file and get paths to all images from it (just value of `src` attribute for `img` tags).
	 *
	 * @param $htmlFileContent content of HTML file to parse.
	 *
	 * @return if successful, then array of images' adresses; otherwise FALSE.
	 */
	public function getAllImagesAddresses($htmlFileContent)
	{
		$dom = new \DOMDocument();

		if (!@$dom->loadHTML($htmlFileContent)) {
			return FALSE;
		}
		
		$images = array();

		foreach($dom->getElementsByTagName('img') as $img) {
      $images[] = $img->getAttribute('src');
		}
		
		return array_unique($images);
	}
	
	/**
	 * Add website prefix to image address if it is missing for every image's address from array $images.
	 *
	 * @param $remoteAddress URL of HTML file where images' addresses are met.
	 * @param $images adresses of images from HTML file which is located on $remoteAddress.
	 *
	 * @return if successful, then array of images' adresses; otherwise FALSE.
	 */
	public function normalizeImagesPaths($remoteAddress, $images)
	{
		$toRemove = array();
		
		$remoteAddressBaseDirSlashPos = strpos($remoteAddress, "/", strpos($remoteAddress, "://") + strlen("://"));
		$remoteAddressCurrentDirSlashPos = strrpos($remoteAddress, "/");
				
		if ($remoteAddressCurrentDirSlashPos === FALSE || $remoteAddressBaseDirSlashPos === FALSE) {
		  return FALSE; // Bad $remoteAddress format
		}
		
		$remoteAddressCurrentDir = substr($remoteAddress, 0, $remoteAddressCurrentDirSlashPos);
		$remoteAddressBaseDir = substr($remoteAddress, 0, $remoteAddressBaseDirSlashPos);
		
		foreach ($images as $key => &$image) {
			if (substr($image, 0, strlen("../")) == "../") {
				$image = $remoteAddressCurrentDir . "/../" . substr($image, strlen("../"));
			} else if (substr($image, 0, strlen("./")) == "./") {
				$image = $remoteAddressBaseDir . "/" . substr($image, strlen("./"));
			} else if (substr($image, 0, strlen("/")) == "/") {
				$image = $remoteAddressBaseDir . "/" . substr($image, strlen("/"));
			}
		}
		
		return $images;
	}
}
