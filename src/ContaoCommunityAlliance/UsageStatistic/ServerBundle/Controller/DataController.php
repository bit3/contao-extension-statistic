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
 * @Route(service="usage_statistic_server.controller.data_controller")
 */
class DataController extends AbstractDataController
{
	/**
	 * @Route(
	 *     "/names.{_format}",
	 *     requirements={"_format"="json"}
	 * )
	 * @Route(
	 *     "/names/{path}.{_format}",
	 *     requirements={"_format"="json", "path"=".*"}
	 * )
	 *
	 * @return Response
	 */
	public function namesAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('n.name')
			->from('UsageStatisticServerBundle:DataName', 'n');

		$this->addPathToQuery($queryBuilder, 'n', $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		$names = array_map(
			function (array $row) {
				return $row['name'];
			},
			$result
		);

		return $this->createResponse($request, $names);
	}
}
