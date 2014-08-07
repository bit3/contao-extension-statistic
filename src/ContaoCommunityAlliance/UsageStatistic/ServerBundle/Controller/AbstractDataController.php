<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Controller;

use ContaoCommunityAlliance\UsageStatistic\ServerBundle\Entity\DataKey;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
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
				->andWhere($expr->like($alias . '.key', ':path'))
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
	protected function createResponse(Request $request, $timeParts, $valueParts, $result)
	{
		$response = new Response();
		$response->setCharset('UTF-8');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		$response->headers->set('Access-Control-Allow-Methods', '*');

		$format = $request->getRequestFormat('json');

		switch ($format) {
			case 'json':
				$valuePart = array_pop($valueParts);
				$lastPart  = array_pop($valueParts);

				$parts = array_merge($timeParts, $valueParts);

				$data = [];
				foreach ($result as $row) {
					$ref = & $data;
					foreach ($parts as $part) {
						$part = $row[$part];
						if (!isset($ref[$part])) {
							$ref[$part] = [];
						}
						$ref = & $ref[$part];
					}

					$ref[$row[$lastPart]] = $row[$valuePart];
				}

				$serialized = $this->serializer->serialize($data, 'json');

				$response->headers->set('Content-Type', sprintf('application/json; charset=UTF-8'));
				$response->setContent($serialized);
				break;


			default:
				throw new FileNotFoundException($request->getPathInfo());
		}

		return $response;
	}
}
