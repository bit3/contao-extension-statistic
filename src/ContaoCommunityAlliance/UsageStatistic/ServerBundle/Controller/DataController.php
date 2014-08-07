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
 * @Route(service="usage_statistic_server.controller.data_controller")
 */
class DataController extends AbstractDataController
{
	/**
	 * @Route(
	 *     "/keys.{_format}",
	 *     requirements={"_format"="json|flat\.json"}
	 * )
	 * @Route(
	 *     "/keys/{path}.{_format}",
	 *     requirements={"_format"="json|flat\.json", "path"=".*"}
	 * )
	 *
	 * @return Response
	 */
	public function keysAction(Request $request, $path = false)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$queryBuilder
			->select('n.key')
			->from('UsageStatisticServerBundle:DataKey', 'n');

		$this->addPathToQuery($queryBuilder, 'n', $path);

		$query  = $queryBuilder->getQuery();
		$result = $query->getResult();

		$keys = array_map(
			function (array $row) {
				return $row['key'];
			},
			$result
		);

		return $this->createResponse($request, $keys);
	}
}
