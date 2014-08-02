<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation AS Serializer;

/**
 * Data
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Table(name="data_names")
 * @ORM\Entity(repositoryClass="ContaoCommunityAlliance\UsageStatistic\ServerBundle\Repository\DataNameRepository")
 */
class DataName
{

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="name", type="string")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\OneToMany(targetEntity="DataValue", mappedBy="name")
	 *
	 * @var Collection
	 */
	private $data;

	public function __construct()
	{
		$this->data = new ArrayCollection();
	}

	/**
	 * Get the data name.
	 *
	 * @return integer
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 *
	 * @return static
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getData()
	{
		return $this->data;
	}
}
