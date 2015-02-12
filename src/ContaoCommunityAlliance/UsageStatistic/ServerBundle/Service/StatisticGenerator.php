<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Service;

use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\WeeklyDataKeySummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\WeeklyDataValueSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\MonthlyDataKeySummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\MonthlyDataValueSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\QuarterlyDataKeySummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\QuarterlyDataValueSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\YearlyDataKeySummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\YearlyDataValueSummary;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class StatisticGenerator
{

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * The memory limit.
	 *
	 * @var int
	 */
	private $memoryLimit = null;

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @param EntityManager $entityManager
	 *
	 * @return static
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		return $this;
	}

	/**
	 * Return the memory limit in bytes.
	 *
	 * @return int
	 */
	private function getMemoryLimit()
	{
		if (null === $this->memoryLimit) {
			$memoryLimit = strtolower(ini_get('memory_limit'));

			if (preg_match('~^(\d+)(tgmk)?$~', $memoryLimit, $matches)) {
				$memoryLimit = $matches[1];
				switch ($matches[2]) {
					case 't':
						$memoryLimit *= 1024;
					// no break
					case 'g':
						$memoryLimit *= 1024;
					// no break
					case 'm':
						$memoryLimit *= 1024;
					// no break
					case 'k':
						$memoryLimit *= 1024;
						break;
				}
			}

			$this->memoryLimit = (int) $memoryLimit;
		}

		return $this->memoryLimit;
	}

	/**
	 * Determine if flush is required due to memory consumtion.
	 *
	 * @return bool
	 */
	private function isFlushRequired()
	{
		$memoryLimit = $this->getMemoryLimit();

		if ($memoryLimit <= 0) {
			return false;
		}

		$memoryUsage = memory_get_usage();

		if ($memoryUsage >= (.8 * $memoryLimit)) {
			return true;
		}

		return false;
	}

	public function generateDaily()
	{
		$this->generateDataKeySummary('daily');
		$this->generateDataValueSummary('daily');
	}

	public function generateWeekly()
	{
		$this->generateDataKeySummary('weekly');
		$this->generateDataValueSummary('weekly');
	}

	public function generateMonthly()
	{
		$this->generateDataKeySummary('monthly');
		$this->generateDataValueSummary('monthly');
	}

	public function generateQuarterly()
	{
		$this->generateDataKeySummary('quarterly');
		$this->generateDataValueSummary('quarterly');
	}

	public function generateYearly()
	{
		$this->generateDataKeySummary('yearly');
		$this->generateDataValueSummary('yearly');
	}

	protected function generateDataKeySummary($timespan)
	{
		switch ($timespan) {
			case 'daily':
				$entityName = 'UsageStatisticServerBundle:DailyDataKeySummary';
				$query      = 'YEAR(v.datetime) AS year, MONTH(v.datetime) AS month, DAY(v.datetime) AS day';
				$fields     = 'year, month, day';
				$keys       = ['year', 'month', 'day', 'key'];
				break;

			case 'weekly':
				$entityName = 'UsageStatisticServerBundle:WeeklyDataKeySummary';
				$query      = 'YEAR(v.datetime) AS year, WEEKOFYEAR(v.datetime) AS week';
				$fields     = 'year, week';
				$keys       = ['year', 'week', 'key'];
				break;

			case 'monthly':
				$entityName = 'UsageStatisticServerBundle:MonthlyDataKeySummary';
				$query      = 'YEAR(v.datetime) AS year, MONTH(v.datetime) AS month';
				$fields     = 'year, month';
				$keys       = ['year', 'month', 'key'];
				break;

			case 'quarterly':
				$entityName = 'UsageStatisticServerBundle:QuarterlyDataKeySummary';
				$query      = 'YEAR(v.datetime) AS year, QUARTER(v.datetime) AS quarter';
				$fields     = 'year, quarter';
				$keys       = ['year', 'quarter', 'key'];
				break;

			case 'yearly':
				$entityName = 'UsageStatisticServerBundle:YearlyDataKeySummary';
				$query      = 'YEAR(v.datetime) AS year';
				$fields     = 'year';
				$keys       = ['year', 'key'];
				break;

			default:
				throw new \InvalidArgumentException();
		}

		$mapping = new ResultSetMapping();
		$mapping->addScalarResult('year', 'year');
		$mapping->addScalarResult('month', 'month');
		$mapping->addScalarResult('quarter', 'quarter');
		$mapping->addScalarResult('week', 'week');
		$mapping->addScalarResult('day', 'day');
		$mapping->addScalarResult('key_name', 'key');
		$mapping->addScalarResult('summary', 'summary');

		$sql = <<<SQL
SELECT $fields, key_name, COUNT(id) AS summary
FROM (
	SELECT $query, i.id, v.key_name
	FROM data_values v
	INNER JOIN installations i
	ON i.id = v.installation
	GROUP BY $fields, i.id, v.key_name
) t
GROUP BY $fields, key_name
SQL;

		$query            = $this->entityManager->createNativeQuery($sql, $mapping);
		$result           = $query->getResult();
		$repository       = $this->entityManager->getRepository($entityName);
		$class            = new \ReflectionClass($repository->getClassName());
		$propertyAccessor = new PropertyAccessor();

		$this->truncate($repository);

		foreach ($result as $row) {
			$entity = $class->newInstance();

			foreach ($keys as $key) {
				$propertyAccessor->setValue($entity, $key, $row[$key]);
			}

			$entity->setSummary($row['summary']);

			$this->entityManager->persist($entity);

			if ($this->isFlushRequired()) {
				$this->entityManager->flush();
				$this->entityManager->clear();
			}
		}

		$this->entityManager->flush();
	}

	protected function generateDataValueSummary($timespan)
	{
		switch ($timespan) {
			case 'daily':
				$entityName = 'UsageStatisticServerBundle:DailyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year, MONTH(v.datetime) AS month, DAY(v.datetime) AS day';
				$fields     = 'year, month, day';
				$keys       = ['year', 'month', 'day', 'key', 'value'];
				break;

			case 'weekly':
				$entityName = 'UsageStatisticServerBundle:WeeklyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year, WEEKOFYEAR(v.datetime) AS week';
				$fields     = 'year, week';
				$keys       = ['year', 'week', 'key', 'value'];
				break;

			case 'monthly':
				$entityName = 'UsageStatisticServerBundle:MonthlyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year, MONTH(v.datetime) AS month';
				$fields     = 'year, month';
				$keys       = ['year', 'month', 'key', 'value'];
				break;

			case 'quarterly':
				$entityName = 'UsageStatisticServerBundle:QuarterlyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year, QUARTER(v.datetime) AS quarter';
				$fields     = 'year, quarter';
				$keys       = ['year', 'quarter', 'key', 'value'];
				break;

			case 'yearly':
				$entityName = 'UsageStatisticServerBundle:YearlyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year';
				$fields     = 'year';
				$keys       = ['year', 'key', 'value'];
				break;

			default:
				throw new \InvalidArgumentException();
		}

		$mapping = new ResultSetMapping();
		$mapping->addScalarResult('year', 'year');
		$mapping->addScalarResult('month', 'month');
		$mapping->addScalarResult('quarter', 'quarter');
		$mapping->addScalarResult('week', 'week');
		$mapping->addScalarResult('day', 'day');
		$mapping->addScalarResult('key_name', 'key');
		$mapping->addScalarResult('value', 'value');
		$mapping->addScalarResult('summary', 'summary');

		$sql = <<<SQL
SELECT $fields, key_name, value, COUNT(id) AS summary
FROM (
	SELECT $query, i.id, v.key_name, v.value
	FROM data_values v
	INNER JOIN installations i
	ON i.id = v.installation
	GROUP BY $fields, i.id, v.key_name, v.value
) t
GROUP BY $fields, key_name, value
SQL;

		$query            = $this->entityManager->createNativeQuery($sql, $mapping);
		$result           = $query->getResult();
		$repository       = $this->entityManager->getRepository($entityName);
		$class            = new \ReflectionClass($repository->getClassName());
		$propertyAccessor = new PropertyAccessor();

		$this->truncate($repository);

		foreach ($result as $row) {
			$entity = $class->newInstance();

			foreach ($keys as $key) {
				$propertyAccessor->setValue($entity, $key, $row[$key]);
			}

			$entity->setSummary($row['summary']);

			$this->entityManager->persist($entity);

			if ($this->isFlushRequired()) {
				$this->entityManager->flush();
				$this->entityManager->clear();
			}
		}

		$this->entityManager->flush();
	}

	protected function truncate(EntityRepository $repository)
	{
		$classMetaData = $this->entityManager->getClassMetadata($repository->getClassName());
		$connection    = $this->entityManager->getConnection();
		$dbPlatform    = $connection->getDatabasePlatform();
		$sql           = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
		$connection->executeUpdate($sql);
	}
}
