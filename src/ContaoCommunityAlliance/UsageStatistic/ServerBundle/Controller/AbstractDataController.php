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

abstract class AbstractDataController extends AbstractEntityManagerAwareController
{

	/**
	 * @var Serializer
	 */
	protected $serializer;

	/**
	 * @return Serializer
	 */
	public function getSerializer()
	{
		return $this->serializer;
	}

	/**
	 * @param Serializer $serializer
	 *
	 * @return DataController
	 */

	public function setSerializer(Serializer $serializer)
	{
		$this->serializer = $serializer;
		return $this;
	}

	protected function addPathToQuery(QueryBuilder $queryBuilder, $alias, $path)
	{
		if ($path) {
			$path = str_replace(
				['%', '*'],
				['\\%', '%'],
				$path
			);

			$expr = $queryBuilder->expr();
			$queryBuilder
				->andWhere($expr->like($alias . '.name', ':path'))
				->setParameter('path', $path);
		}
	}

	/**
	 * Serialize the data in the requested format and create a response object.
	 *
	 * @param Request $request
	 * @param mixed   $data
	 *
	 * @return Response
	 */
	protected function createResponse(Request $request, $data)
	{
		$format = $request->getRequestFormat('json');

		$serialized = $this->serializer->serialize($data, $format);

		$response = new Response();
		$response->setCharset('UTF-8');
		$response->headers->set('Content-Type', sprintf('application/%s; charset=UTF-8', $format));
		$response->headers->set('Access-Control-Allow-Origin', '*');
		$response->headers->set('Access-Control-Allow-Methods', '*');
		$response->setContent($serialized);

		return $response;
	}
}
