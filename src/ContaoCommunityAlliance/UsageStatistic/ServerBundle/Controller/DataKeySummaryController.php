<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Controller;

use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\DataKey;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service="usage_statistic_server.controller.data_key_summary_controller")
 */
class DataKeySummaryController extends AbstractDataController
{

	/**
	 * @Route(
	 *     "/summary/keys/{year}w{week}.{_format}",
	 *     requirements={"year"="\d{4}", "week"="\d{1,2}", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/keys/{year}w{week}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "week"="\d{1,2}", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function weeklyDataKeySummaryAction(Request $request, $year, $week, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.key', 's.summary')
			->from('UsageStatisticServerBundle:WeeklyDataKeySummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.week', ':week'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('week', $week, Type::INTEGER);

		return $this->abstractDataKeySummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/{year}-{month}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/keys/{year}-{month}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function monthlyDataKeySummaryAction(Request $request, $year, $month, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.key', 's.summary')
			->from('UsageStatisticServerBundle:MonthlyDataKeySummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.month', ':month'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('month', $month, Type::INTEGER);

		return $this->abstractDataKeySummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/{year}q{quarter}.{_format}",
	 *     requirements={"year"="\d{4}", "quarter"="\d", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/keys/{year}q{quarter}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "quarter"="\d", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function quarterlyDataKeySummaryAction(Request $request, $year, $quarter, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.key', 's.summary')
			->from('UsageStatisticServerBundle:QuarterlyDataKeySummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.quarter', ':quarter'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('quarter', $quarter, Type::INTEGER);

		return $this->abstractDataKeySummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/{year}.{_format}",
	 *     requirements={"year"="\d{4}", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/keys/{year}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function yearlyDataKeySummaryAction(Request $request, $year, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.key', 's.summary')
			->from('UsageStatisticServerBundle:YearlyDataKeySummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->setParameter('year', $year, Type::INTEGER);

		return $this->abstractDataKeySummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * Generic abstract data key summary action, that finally complete these kind of actions.
	 *
	 * @param Request      $request
	 * @param QueryBuilder $queryBuilder
	 * @param string       $alias
	 * @param string|false $path
	 *
	 * @return Response
	 */
	protected function abstractDataKeySummaryAction(Request $request, QueryBuilder $queryBuilder, $alias, $path)
	{
		$queryBuilder->orderBy($alias . '.key');
		$this->addPathToQuery($queryBuilder, $alias, $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		$summaries = [];
		foreach ($result as $row) {
			$summaries[$row['key']] = $row['summary'];
		}

		return $this->createResponse($request, $summaries);
	}
}