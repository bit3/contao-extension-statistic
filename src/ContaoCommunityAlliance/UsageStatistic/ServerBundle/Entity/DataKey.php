<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation AS Serializer;

/**
 * DataKey
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Table(name="data_keys")
 * @ORM\Entity(repositoryClass="ContaoCommunityAlliance\UsageStatistic\ServerBundle\Repository\DataKeyRepository")
 */
class DataKey
{

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="key_name", type="string")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var string
	 */
	private $key;

	/**
	 * @ORM\OneToMany(targetEntity="DataValue", mappedBy="key")
	 *
	 * @var Collection
	 */
	private $data;

	public function __construct()
	{
		$this->data = new ArrayCollection();
	}

	/**
	 * Get the data key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 *
	 * @return static
	 */
	public function setKey($key)
	{
		$this->key = (string) $key;
		return $this;
	}

	/**
	 * Get all data values associated with this key.
	 *
	 * @return Collection
	 */
	public function getData()
	{
		return $this->data;
	}
}
