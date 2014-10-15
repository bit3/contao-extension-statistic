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
 * @Route(service="usage_statistic_server.controller.data_value_summary_controller")
 */
class DataValueSummaryController extends AbstractDataController
{

	/**
	 * @Route(
	 *     "/summary/values/{year}-{month}-{day}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/values/{year}-{month}-{day}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "day"="\d{1,2}", "path"=".+?", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function dailyDataValueSummaryAction(Request $request, $year, $month, $day, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.day', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:DailyDataValueSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.month', ':month'))
			->andWhere($queryBuilder->expr()->eq('s.day', ':day'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('month', $month, Type::INTEGER)
			->setParameter('day', $day, Type::INTEGER);

		return $this->abstractDataValueSummaryAction($request, $queryBuilder, ['year', 'month', 'day'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/{year}w{week}.{_format}",
	 *     requirements={"year"="\d{4}", "week"="\d{1,2}", "_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/values/{year}w{week}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "week"="\d{1,2}", "path"=".+?", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function weeklyDataValueSummaryAction(Request $request, $year, $week, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.week', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:WeeklyDataValueSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.week', ':week'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('week', $week, Type::INTEGER);

		return $this->abstractDataValueSummaryAction($request, $queryBuilder, ['year', 'week'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/{year}-{month}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/values/{year}-{month}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "month"="\d{1,2}", "path"=".+?", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function monthlyDataValueSummaryAction(Request $request, $year, $month, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:MonthlyDataValueSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.month', ':month'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('month', $month, Type::INTEGER);

		return $this->abstractDataValueSummaryAction($request, $queryBuilder, ['year', 'month'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/{year}q{quarter}.{_format}",
	 *     requirements={"year"="\d{4}", "quarter"="\d", "_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/values/{year}q{quarter}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "quarter"="\d", "path"=".+?", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function quarterlyDataValueSummaryAction(Request $request, $year, $quarter, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.quarter', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:QuarterlyDataValueSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->andWhere($queryBuilder->expr()->eq('s.quarter', ':quarter'))
			->setParameter('year', $year, Type::INTEGER)
			->setParameter('quarter', $quarter, Type::INTEGER);

		return $this->abstractDataValueSummaryAction($request, $queryBuilder, ['year', 'quarter'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/{year}.{_format}",
	 *     requirements={"year"="\d{4}", "_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/values/{year}/{path}.{_format}",
	 *     requirements={"year"="\d{4}", "path"=".+?", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function yearlyDataValueSummaryAction(Request $request, $year, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:YearlyDataValueSummary', 's')
			->where($queryBuilder->expr()->eq('s.year', ':year'))
			->setParameter('year', $year, Type::INTEGER);

		return $this->abstractDataValueSummaryAction($request, $queryBuilder, ['year'], 's', $path);
	}

	/**
	 * Generic abstract data value summary action, that finally complete these kind of actions.
	 *
	 * @param Request      $request
	 * @param QueryBuilder $queryBuilder
	 * @param string       $alias
	 * @param string|false $path
	 *
	 * @return Response
	 */
	protected function abstractDataValueSummaryAction(Request $request, QueryBuilder $queryBuilder, $parts, $alias, $path)
	{
		$queryBuilder
			->orderBy($alias . '.key')
			->addOrderBy($alias . '.value');
		$this->addPathToQuery($queryBuilder, $alias, $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		return $this->createResponse($request, $parts, ['key', 'value', 'summary'], $result);
	}
}
