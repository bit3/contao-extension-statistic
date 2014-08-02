<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Controller;

use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\DataName;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service="usage_statistic_server.controller.data_name_summary_controller")
 */
class DataNameSummaryController extends AbstractDataController
{

	/**
	 * @Route(
	 *     "/summary/names/{year}w{week}.{_format}",
	 *     requirements={"year"="\d{4}", "week"="\d{1,2}", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/{year}w{week}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "week"="\d{1,2}", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function weeklyDataNameSummaryAction(Request $request, $year, $week, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.name', 's.summary')
			->from('UsageStatisticServerBundle:WeeklyDataNameSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.week', ':week'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('week', $week, Type::INTEGER);

		return $this->abstractDataNameSummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/names/{year}-{month}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/{year}-{month}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function monthlyDataNameSummaryAction(Request $request, $year, $month, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.name', 's.summary')
			->from('UsageStatisticServerBundle:MonthlyDataNameSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.month', ':month'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('month', $month, Type::INTEGER);

		return $this->abstractDataNameSummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/names/{year}q{quarter}.{_format}",
	 *     requirements={"year"="\d{4}", "quarter"="\d", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/{year}q{quarter}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "quarter"="\d", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function quarterlyDataNameSummaryAction(Request $request, $year, $quarter, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.name', 's.summary')
			->from('UsageStatisticServerBundle:QuarterlyDataNameSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.quarter', ':quarter'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('quarter', $quarter, Type::INTEGER);

		return $this->abstractDataNameSummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/names/{year}.{_format}",
	 *     requirements={"year"="\d{4}", "_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/{year}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function yearlyDataNameSummaryAction(Request $request, $year, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.name', 's.summary')
			->from('UsageStatisticServerBundle:YearlyDataNameSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->setParameter('year', $year, Type::INTEGER);

		return $this->abstractDataNameSummaryAction($request, $queryBuilder, 's', $path);
	}

	/**
	 * Generic abstract data name summary action, that finally complete these kind of actions.
	 *
	 * @param Request      $request
	 * @param QueryBuilder $queryBuilder
	 * @param string       $alias
	 * @param string|false $path
	 *
	 * @return Response
	 */
	protected function abstractDataNameSummaryAction(Request $request, QueryBuilder $queryBuilder, $alias, $path)
	{
		$queryBuilder->orderBy($alias . '.name');
		$this->addPathToQuery($queryBuilder, $alias, $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		$summaries = [];
		foreach ($result as $row) {
			$summaries[$row['name']] = $row['summary'];
		}

		return $this->createResponse($request, $summaries);
	}
}
