<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation AS Serializer;

/**
 * MonthlyDataKeySummary
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @ORM\Table(name="summary_data_key_monthly")
 * @ORM\Entity(repositoryClass="ContaoCommunityAlliance\UsageStatistic\ServerBundle\Repository\MonthlyDataKeySummaryRepository")
 */
class MonthlyDataKeySummary
{

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="year", type="integer")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var int
	 */
	private $year;

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="month", type="integer")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var int
	 */
	private $month;

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Id
	 * @ORM\Column(name="key", type="string")
	 * @ORM\GeneratedValue(strategy="NONE")
	 *
	 * @var string
	 */
	private $key;

	/**
	 * @Serializer\Expose
	 *
	 * @ORM\Column(name="summary", type="integer")
	 *
	 * @var int
	 */
	private $summary;

	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * @param int $year
	 *
	 * @return static
	 */
	public function setYear($year)
	{
		$this->year = (int) $year;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMonth()
	{
		return $this->month;
	}

	/**
	 * @param int $month
	 *
	 * @return static
	 */
	public function setMonth($month)
	{
		$this->month = (int) $month;
		return $this;
	}

	/**
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
	 * @return int
	 */
	public function getSummary()
	{
		return $this->summary;
	}

	/**
	 * @param int $summary
	 *
	 * @return static
	 */
	public function setSummary($summary)
	{
		$this->summary = (int) $summary;
		return $this;
	}
}
