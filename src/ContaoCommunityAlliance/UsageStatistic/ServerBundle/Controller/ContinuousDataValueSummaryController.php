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
 * @Route(service="usage_statistic_server.controller.continuous_data_value_summary_controller")
 */
class ContinuousDataValueSummaryController extends AbstractDataController
{

	/**
	 * @Route(
	 *     "/summary/values/daily.{_format}",
	 *     requirements={"_format"="json|flat"}
	 * )
	 * @Route(
	 *     "/summary/values/daily/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json|flat"}
	 * )
	 *
	 * @return Response
	 */
	public function dailyContinuousDataValueSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.day', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:DailyDataValueSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.month')
			->addOrderBy('s.day');

		return $this->abstractContinuousDataValueSummaryAction($request, $queryBuilder, ['year', 'month', 'day'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/weekly.{_format}",
	 *     requirements={"_format"="json|flat"}
	 * )
	 * @Route(
	 *     "/summary/values/weekly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json|flat"}
	 * )
	 *
	 * @return Response
	 */
	public function weeklyContinuousDataValueSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.week', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:WeeklyDataValueSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.week');

		return $this->abstractContinuousDataValueSummaryAction($request, $queryBuilder, ['year', 'week'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/monthly.{_format}",
	 *     requirements={"_format"="json|flat"}
	 * )
	 * @Route(
	 *     "/summary/values/monthly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json|flat"}
	 * )
	 *
	 * @return Response
	 */
	public function monthlyContinuousDataValueSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:MonthlyDataValueSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.month');

		return $this->abstractContinuousDataValueSummaryAction($request, $queryBuilder, ['year', 'month'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/quarterly.{_format}",
	 *     requirements={"_format"="json|flat"}
	 * )
	 * @Route(
	 *     "/summary/values/quarterly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json|flat"}
	 * )
	 *
	 * @return Response
	 */
	public function quarterlyContinuousDataValueSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.quarter', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:QuarterlyDataValueSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.quarter');

		return $this->abstractContinuousDataValueSummaryAction($request, $queryBuilder, ['year', 'quarter'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/values/yearly.{_format}",
	 *     requirements={"_format"="json|flat"}
	 * )
	 * @Route(
	 *     "/summary/values/yearly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json|flat"}
	 * )
	 *
	 * @return Response
	 */
	public function yearlyContinuousDataValueSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.key', 's.value', 's.summary')
			->from('UsageStatisticServerBundle:YearlyDataValueSummary', 's')
			->orderBy('s.year');

		return $this->abstractContinuousDataValueSummaryAction($request, $queryBuilder, ['year'], 's', $path);
	}

	/**
	 * Generic abstract data value summary action, that finally complete these kind of actions.
	 *
	 * @param Request      $request
	 * @param QueryBuilder $queryBuilder
	 * @param array        $parts
	 * @param string       $alias
	 * @param string|false $path
	 *
	 * @return Response
	 */
	protected function abstractContinuousDataValueSummaryAction(
		Request $request,
		QueryBuilder $queryBuilder,
		array $parts,
		$alias,
		$path
	) {
		$queryBuilder
			->addOrderBy($alias . '.key')
			->addOrderBy($alias . '.value');
		$this->addPathToQuery($queryBuilder, $alias, $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		return $this->createResponse($request, $parts, ['key', 'value', 'summary'], $result);
	}
}
