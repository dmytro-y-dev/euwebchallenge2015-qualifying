<?php

namespace JobScheduler\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="JobScheduler\Entity\JobRepository")
 * @ORM\Table(name="jobs")
 */
class Job
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $htmlpage;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $dateFinished;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $dateStarted;
	
	/**
	 * @ORM\Column(type="string", length=30, nullable=false)
	 */
	private $status;
	
	/**
	 * @ORM\OneToMany(targetEntity="Image", mappedBy="job")
	 */
	private $images;
	
	public function __construct($htmlpage)
	{
		$this->htmlpage = $htmlpage;
		$this->images = new ArrayCollection();
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setHtmlPage($htmlpage)
	{
		$this->htmlpage = $htmlpage;
	}
	
	public function getHtmlPage()
	{
		return $this->htmlpage;
	}
	
	public function setDateFinished($dateFinished)
	{
		$this->dateFinished = $dateFinished;
	}
	
	public function getDateFinished()
	{
		return $this->dateFinished;
	}
	
	public function setDateStarted($dateStarted)
	{
		$this->dateStarted = $dateStarted;
	}
	
	public function getDateStarted()
	{
		return $this->dateStarted;
	}
	
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
  public function addImage(Image $image)
  {
  	$this->images->add($image);

		return $this;
  }

  public function removeImage(Image $image)
  {
		$this->images->removeElement($image);
  }

  public function getImages()
  {
    return $this->images;
  }
}
