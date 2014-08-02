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
	 * @ORM\ManyToOne(targetEntity="DataName", inversedBy="data")
	 * @ORM\JoinColumn(name="name", referencedColumnName="name")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var DataName
	 */
	private $name;

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
	 * Get the data name.
	 *
	 * @return DataName
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param DataName $name
	 *
	 * @return static
	 */
	public function setName(DataName $name)
	{
		$this->name = $name;
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
