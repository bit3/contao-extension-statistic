<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation AS Serializer;

/**
 * Installation
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Table(name="installations")
 * @ORM\Entity(repositoryClass="ContaoCommunityAlliance\UsageStatistic\ServerBundle\Repository\InstallationRepository")
 */
class Installation
{

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="id", type="string", length=128, options={"fixed"=true})
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var string
	 */
	private $id;

	/**
	 * @ORM\OneToMany(targetEntity="DataValue", mappedBy="installation")
	 *
	 * @var Collection
	 */
	private $data;

	public function __construct()
	{
		$this->data = new ArrayCollection();
	}

	/**
	 * Get the installation id.
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set the installation id.
	 *
	 * @param string $id
	 *
	 * @return static
	 */
	public function setId($id)
	{
		$this->id = (string) $id;
		return $this;
	}

	/**
	 * Return all data, collected in this installation.
	 *
	 * @return Collection
	 */
	public function getData()
	{
		return $this->data;
	}
}
