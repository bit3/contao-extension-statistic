<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Service;

use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\WeeklyDataNameSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\WeeklyDataValueSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\MonthlyDataNameSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\MonthlyDataValueSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\QuarterlyDataNameSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\QuarterlyDataValueSummary;
use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\YearlyDataNameSummary;
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
		$this->generateDataNameSummary('weekly');
		$this->generateDataValueSummary('weekly');
	}

	public function generateMonthly()
	{
		$this->generateDataNameSummary('monthly');
		$this->generateDataValueSummary('monthly');
	}

	public function generateQuarterly()
	{
		$this->generateDataNameSummary('quarterly');
		$this->generateDataValueSummary('quarterly');
	}

	public function generateYearly()
	{
		$this->generateDataNameSummary('yearly');
		$this->generateDataValueSummary('yearly');
	}

	protected function generateDataNameSummary($timespan)
	{
		switch ($timespan) {
			case 'weekly':
				$entityName = 'UsageStatisticServerBundle:WeeklyDataNameSummary';
				$query      = 'YEAR(v.datetime) AS year, WEEKOFYEAR(v.datetime) AS week';
				$fields     = 'year, week';
				$keys       = ['year', 'week', 'name'];
				break;

			case 'monthly':
				$entityName = 'UsageStatisticServerBundle:MonthlyDataNameSummary';
				$query      = 'YEAR(v.datetime) AS year, MONTH(v.datetime) AS month';
				$fields     = 'year, month';
				$keys       = ['year', 'month', 'name'];
				break;

			case 'quarterly':
				$entityName = 'UsageStatisticServerBundle:QuarterlyDataNameSummary';
				$query      = 'YEAR(v.datetime) AS year, QUARTER(v.datetime) AS quarter';
				$fields     = 'year, quarter';
				$keys       = ['year', 'quarter', 'name'];
				break;

			case 'yearly':
				$entityName = 'UsageStatisticServerBundle:YearlyDataNameSummary';
				$query      = 'YEAR(v.datetime) AS year';
				$fields     = 'year';
				$keys       = ['year', 'name'];
				break;

			default:
				throw new \InvalidArgumentException();
		}

		$mapping = new ResultSetMapping();
		$mapping->addScalarResult('year', 'year');
		$mapping->addScalarResult('month', 'month');
		$mapping->addScalarResult('quarter', 'quarter');
		$mapping->addScalarResult('week', 'week');
		$mapping->addScalarResult('name', 'name');
		$mapping->addScalarResult('summary', 'summary');

		$sql = <<<SQL
SELECT $fields, name, COUNT(id) AS summary
FROM (
	SELECT $query, i.id, v.name
	FROM data_values v
	INNER JOIN installations i
	ON i.id = v.installation
	GROUP BY $fields, i.id, v.name
) t
GROUP BY $fields, name
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
				$keys       = ['year', 'week', 'name', 'value'];
				break;

			case 'monthly':
				$entityName = 'UsageStatisticServerBundle:MonthlyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year, MONTH(v.datetime) AS month';
				$fields     = 'year, month';
				$keys       = ['year', 'month', 'name', 'value'];
				break;

			case 'quarterly':
				$entityName = 'UsageStatisticServerBundle:QuarterlyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year, QUARTER(v.datetime) AS quarter';
				$fields     = 'year, quarter';
				$keys       = ['year', 'quarter', 'name', 'value'];
				break;

			case 'yearly':
				$entityName = 'UsageStatisticServerBundle:YearlyDataValueSummary';
				$query      = 'YEAR(v.datetime) AS year';
				$fields     = 'year';
				$keys       = ['year', 'name', 'value'];
				break;

			default:
				throw new \InvalidArgumentException();
		}

		$mapping = new ResultSetMapping();
		$mapping->addScalarResult('year', 'year');
		$mapping->addScalarResult('month', 'month');
		$mapping->addScalarResult('quarter', 'quarter');
		$mapping->addScalarResult('week', 'week');
		$mapping->addScalarResult('name', 'name');
		$mapping->addScalarResult('value', 'value');
		$mapping->addScalarResult('summary', 'summary');

		$sql = <<<SQL
SELECT $fields, name, value, COUNT(id) AS summary
FROM (
	SELECT $query, i.id, v.name, v.value
	FROM data_values v
	INNER JOIN installations i
	ON i.id = v.installation
	GROUP BY $fields, i.id, v.name, v.value
) t
GROUP BY $fields, name, value
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
