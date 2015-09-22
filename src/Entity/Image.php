<?php

namespace JobScheduler\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="JobScheduler\Entity\ImageRepository")
 * @ORM\Table(name="images")
 */
class Image
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Job", inversedBy="images")
	 */
	private $job;
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=255)
	 */
	private $remoteAddress;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $localAddress;
	
	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $contentType;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $width;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $height;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $size;
	
	/**
	 * @ORM\Column(type="string", length=30, nullable=false)
	 */
	private $status;
	
	public function __construct($job, $remoteAddress)
	{
		$this->job = $job;
		$this->remoteAddress = $remoteAddress;
	}
	
	public function setJob($job)
	{
		$this->job = $job;
	}
	
	public function getJob()
	{
		return $this->job;
	}
	
	public function setRemoteAddress($remoteAddress)
	{
		$this->remoteAddress = $remoteAddress;
	}
	
	public function getRemoteAddress()
	{
		return $this->remoteAddress;
	}
	
	public function setLocalAddress($localAddress)
	{
		$this->localAddress = $localAddress;
	}
	
	public function getLocalAddress()
	{
		return $this->localAddress;
	}
	
	public function setContentType($contentType)
	{
		$this->contentType = $contentType;
	}
	
	public function getContentType()
	{
		return $this->contentType;
	}
	
	public function setWidth($width)
	{
		$this->width = $width;
	}
	
	public function getWidth()
	{
		return $this->width;
	}
	
	public function setHeight($height)
	{
		$this->height = $height;
	}
	
	public function getHeight()
	{
		return $this->height;
	}
	
	public function setSize($size)
	{
		$this->size = $size;
	}
	
	public function getSize()
	{
		return $this->size;
	}
	
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
}
