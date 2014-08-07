<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation AS Serializer;

/**
 * DataValue
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Table(name="data_values")
 * @ORM\Entity(repositoryClass="ContaoCommunityAlliance\UsageStatistic\ServerBundle\Repository\DataValueRepository")
 */
class DataValue
{

	/**
	 * @Serializer\Expose
	 * @Serializer\Inline
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Installation", inversedBy="data")
	 * @ORM\JoinColumn(name="installation", referencedColumnName="id")
	 *
	 * @var Installation
	 */
	private $installation;

	/**
	 * @Serializer\Expose
	 * @Serializer\Inline
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="DataKey", inversedBy="data")
	 * @ORM\JoinColumn(name="key_name", referencedColumnName="key_name")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var DataKey
	 */
	private $key;

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="datetime", type="string", length=19)
	 *
	 * @var string
	 */
	private $datetime;

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Column(name="value", type="string")
	 *
	 * @var string
	 */
	private $value;

	/**
	 * @return Installation
	 */
	public function getInstallation()
	{
		return $this->installation;
	}

	/**
	 * @param Installation $installation
	 *
	 * @return static
	 */
	public function setInstallation(Installation $installation)
	{
		$this->installation = $installation;
		return $this;
	}

	/**
	 * Get the data key.
	 *
	 * @return DataKey
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param DataKey $key
	 *
	 * @return static
	 */
	public function setKey(DataKey $key)
	{
		$this->key = $key;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getDatetime()
	{
		return new \DateTime($this->datetime);
	}

	/**
	 * @param \DateTime $datetime
	 *
	 * @return static
	 */
	public function setDatetime(\DateTime $datetime)
	{
		$this->datetime = $datetime->format('Y-m-d H:i:s');
		return $this;
	}

	/**
	 * Get value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Set value
	 *
	 * @param string $value
	 *
	 * @return static
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}
}
