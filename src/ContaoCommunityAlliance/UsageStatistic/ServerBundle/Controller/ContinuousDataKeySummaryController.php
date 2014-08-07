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
 * @Route(service="usage_statistic_server.controller.continuous_data_key_summary_controller")
 */
class ContinuousDataKeySummaryController extends AbstractDataController
{

	/**
	 * @Route(
	 *     "/summary/keys/daily.{_format}",
	 *     requirements={"_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/keys/daily/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function dailyContinuousDataKeySummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.day', 's.key', 's.summary')
			->from('UsageStatisticServerBundle:DailyDataKeySummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.month')
			->addOrderBy('s.day');

		return $this->abstractContinuousDataKeySummaryAction($request, $queryBuilder, ['year', 'month', 'day'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/weekly.{_format}",
	 *     requirements={"_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/keys/weekly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function weeklyContinuousDataKeySummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.week', 's.key', 's.summary')
			->from('UsageStatisticServerBundle:WeeklyDataKeySummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.week');

		return $this->abstractContinuousDataKeySummaryAction($request, $queryBuilder, ['year', 'week'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/monthly.{_format}",
	 *     requirements={"_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/keys/monthly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function monthlyContinuousDataKeySummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.key', 's.summary')
			->from('UsageStatisticServerBundle:MonthlyDataKeySummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.month');

		return $this->abstractContinuousDataKeySummaryAction($request, $queryBuilder, ['year', 'month'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/quarterly.{_format}",
	 *     requirements={"_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/keys/quarterly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function quarterlyContinuousDataKeySummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.quarter', 's.key', 's.summary')
			->from('UsageStatisticServerBundle:QuarterlyDataKeySummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.quarter');

		return $this->abstractContinuousDataKeySummaryAction($request, $queryBuilder, ['year', 'quarter'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/keys/yearly.{_format}",
	 *     requirements={"_format"="(flat\.)?(json|yml)"}
	 * )
	 * @Route(
	 *     "/summary/keys/yearly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="(flat\.)?(json|yml)"}
	 * )
	 *
	 * @return Response
	 */
	public function yearlyContinuousDataKeySummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.key', 's.summary')
			->from('UsageStatisticServerBundle:YearlyDataKeySummary', 's')
			->orderBy('s.year');

		return $this->abstractContinuousDataKeySummaryAction($request, $queryBuilder, ['year'], 's', $path);
	}

	/**
	 * Generic abstract data key summary action, that finally complete these kind of actions.
	 *
	 * @param Request      $request
	 * @param QueryBuilder $queryBuilder
	 * @param array        $parts
	 * @param string       $alias
	 * @param string|false $path
	 *
	 * @return Response
	 */
	protected function abstractContinuousDataKeySummaryAction(
		Request $request,
		QueryBuilder $queryBuilder,
		array $parts,
		$alias,
		$path
	) {
		$queryBuilder
			->addOrderBy($alias . '.key');
		$this->addPathToQuery($queryBuilder, $alias, $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		return $this->createResponse($request, $parts, ['key', 'summary'], $result);
	}
}
