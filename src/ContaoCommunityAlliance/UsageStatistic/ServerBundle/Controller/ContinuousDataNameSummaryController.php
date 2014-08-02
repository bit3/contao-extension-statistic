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
 * @Route(service="usage_statistic_server.controller.continuous_data_name_summary_controller")
 */
class ContinuousDataNameSummaryController extends AbstractDataController
{

	/**
	 * @Route(
	 *     "/summary/names/weekly.{_format}",
	 *     requirements={"_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/weekly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function weeklyContinuousDataNameSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.week', 's.name', 's.summary')
			->from('UsageStatisticServerBundle:WeeklyDataNameSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.week');

		return $this->abstractContinuousDataNameSummaryAction($request, $queryBuilder, ['year', 'week'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/names/monthly.{_format}",
	 *     requirements={"_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/monthly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function monthlyContinuousDataNameSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.month', 's.name', 's.summary')
			->from('UsageStatisticServerBundle:MonthlyDataNameSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.month');

		return $this->abstractContinuousDataNameSummaryAction($request, $queryBuilder, ['year', 'month'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/names/quarterly.{_format}",
	 *     requirements={"_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/quarterly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function quarterlyContinuousDataNameSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.quarter', 's.name', 's.summary')
			->from('UsageStatisticServerBundle:QuarterlyDataNameSummary', 's')
			->orderBy('s.year')
			->addOrderBy('s.quarter');

		return $this->abstractContinuousDataNameSummaryAction($request, $queryBuilder, ['year', 'quarter'], 's', $path);
	}

	/**
	 * @Route(
	 *     "/summary/names/yearly.{_format}",
	 *     requirements={"_format"="json"}
	 * )
	 * @Route(
	 *     "/summary/names/yearly/{path}.{_format}",
	 *     requirements={"path"=".*", "_format"="json"}
	 * )
	 *
	 * @return Response
	 */
	public function yearlyContinuousDataNameSummaryAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('s.year', 's.name', 's.summary')
			->from('UsageStatisticServerBundle:YearlyDataNameSummary', 's')
			->orderBy('s.year');

		return $this->abstractContinuousDataNameSummaryAction($request, $queryBuilder, ['year'], 's', $path);
	}

	/**
	 * Generic abstract data name summary action, that finally complete these kind of actions.
	 *
	 * @param Request      $request
	 * @param QueryBuilder $queryBuilder
	 * @param array        $parts
	 * @param string       $alias
	 * @param string|false $path
	 *
	 * @return Response
	 */
	protected function abstractContinuousDataNameSummaryAction(
		Request $request,
		QueryBuilder $queryBuilder,
		array $parts,
		$alias,
		$path
	) {
		$queryBuilder
			->addOrderBy($alias . '.name');
		$this->addPathToQuery($queryBuilder, $alias, $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		$summaries = [];
		foreach ($result as $row) {
			$ref = & $summaries;
			foreach ($parts as $part) {
				$part = $row[$part];
				if (!isset($ref[$part])) {
					$ref[$part] = [];
				}
				$ref = & $ref[$part];
			}

			$ref[$row['name']] = $row['summary'];
		}

		return $this->createResponse($request, $summaries);
	}
}
