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
		$mapping->addScalarResult('key', 'key');
		$mapping->addScalarResult('summary', 'summary');

		$sql = <<<SQL
SELECT $fields, key, COUNT(id) AS summary
FROM (
	SELECT $query, i.id, v.key
	FROM data_values v
	INNER JOIN installations i
	ON i.id = v.installation
	GROUP BY $fields, i.id, v.key
) t
GROUP BY $fields, key
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
		}

		$this->entityManager->flush();
	}

	protected function generateDataValueSummary($timespan)
	{
		switch ($timespan) {
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
		$mapping->addScalarResult('key', 'key');
		$mapping->addScalarResult('value', 'value');
		$mapping->addScalarResult('summary', 'summary');

		$sql = <<<SQL
SELECT $fields, key, value, COUNT(id) AS summary
FROM (
	SELECT $query, i.id, v.key, v.value
	FROM data_values v
	INNER JOIN installations i
	ON i.id = v.installation
	GROUP BY $fields, i.id, v.key, v.value
) t
GROUP BY $fields, key, value
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
