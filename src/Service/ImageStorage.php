<?php

namespace JobScheduler\Service;

/**
 * This class saves downloaded images to storage and provides information about their content.
 */
class ImageStorage
{
	private $pathToStorage;
	
	/**
	 * Default constructor.
	 *
	 * @param $pathToStorage path where new images should be stored.
	 */
	public function __construct($pathToStorage)
	{
		$this->pathToStorage = $pathToStorage;
	}
	
	/**
	 * Get content type, width, height and size of the image.
	 *
	 * @param $imageContent content of the image file.
	 *
	 * @return if successful, then array which contains information about content type, width, height and size of the image; otherwise FALSE.
	 */
	public function getImageInfo($imageContent)
	{
		$gdImage = @imagecreatefromstring($imageContent);
		
		if ($gdImage === FALSE) {
			return FALSE;
		}
		
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		
		$imageInfo = array(
			'content-type' => $finfo->buffer($imageContent),
			'width' => imagesx($gdImage),
			'height' => imagesy($gdImage),
			'size' => strlen($imageContent)
		);
		
		imagedestroy($gdImage);
		
		return $imageInfo;
	}
	
	/**
	 * Save image to storage.
	 *
	 * @param $jobId job for which image was downloaded.
	 * @param $imageContent content of the image file.
	 *
	 * @return if successful, then local path to image (where image has been saved); otherwise FALSE.
	 */
	public function saveImageToStorage($jobId, $imageContent)
	{		
		static $mappingContentTypeToExtension = array(
			"image/png" => ".png",
			"image/jpeg" => ".jpg",
			"image/gif" => ".gif"
		);
		
		// Get current file content type
		
  	$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$imageContentType = $finfo->buffer($imageContent);
		
		// If it is not an image, then exit
		
		if (!array_key_exists($imageContentType, $mappingContentTypeToExtension)) {
			return FALSE;
		}
		
		// Generate path where to store image
		
		$jobStorage = $this->pathToStorage . "/" . $jobId;
		
		if (!file_exists($jobStorage)) {
			mkdir($jobStorage);
		}
		
		$fi = new \FilesystemIterator($jobStorage, \FilesystemIterator::SKIP_DOTS);
		
		$fileName = iterator_count($fi) + 1;
		$fileExtension = $mappingContentTypeToExtension[$imageContentType];
		
		$fileFullPath = $jobStorage . "/" . $fileName . $fileExtension;
		
		// Save image on file system
		
		file_put_contents($fileFullPath, $imageContent);
		
		return $fileFullPath;
	}
}

