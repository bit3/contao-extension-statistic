<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Controller;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service="usage_statistic_server.controller.core_over_time_controller")
 */
class CoreVersionsOverTimeController extends AbstractEntityManagerAwareController
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

    /**
     * @Route(
     *     "/core-over-time.{_format}",
     *     requirements={"_format"="(json|yml)"}
     * )
     *
     * @return Response
     */
    public function coreOverTimeAction(Request $request)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('s.year', 's.month', 's.key', 's.value', 's.summary')
            ->from('UsageStatisticServerBundle:MonthlyDataValueSummary', 's');

        $queryBuilder
            ->orderBy('s.year')
            ->addOrderBy('s.month')
            ->addOrderBy('s.value');
        $expr = $queryBuilder->expr();
        $queryBuilder
            ->andWhere($expr->like('s.key', ':path'))
            ->setParameter('path', 'contao/co%');

        $query  = $queryBuilder->getQuery();
        $result = $query->getResult();

        return $this->createResponse($request, $result);
    }

    /**
     * Serialize the data in the requested format and create a response object.
     *
     * @param Request $request
     * @param mixed   $data
     *
     * @return Response
     */
    protected function createResponse(Request $request, $result)
    {
        $response = new Response();
        $response->setCharset('UTF-8');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', '*');

        $dataFormat = $request->getRequestFormat('json');

        // labels = list of months
        // series = [version: [month-1, month-2, ...]]
        $data = [
            'labels' => [],
            'series' => []
        ];
        foreach ($result as $row) {
            $label   = $row['year'] . '-' . $row['month'];
            $version = implode('.', array_slice(explode('.', $row['value']), 0, 2));

            if (!in_array($label, $data['labels'])) {
                $data['labels'][] = $label;
            }
            $column = array_search($label, $data['labels']);

            if (!isset($data['series'][$version])) {
                $data['series'][$version] = array_fill(0, count($data['labels']), 0);
            }

            if (!isset($data['series'][$version][$column])) {
                $data['series'][$version][$column] = 0;
            }
            $data['series'][$version][$column] += $row['summary'];
        }

        // Now normalize the table - we might have empty cells.
        foreach (array_keys($data['series']) as $version) {
            foreach (array_keys($data['labels']) as $column) {
                if (!isset($data['series'][$version][$column])) {
                    $data['series'][$version][$column] = 0;
                }
            }
        }

        ksort($data['series']);

        switch ($dataFormat) {
            case 'json':
                $serialized = $this->serializer->serialize($data, 'json');

                $response->headers->set('Content-Type', sprintf('application/json; charset=UTF-8'));
                $response->setContent($serialized);
                break;

            case 'yml':
                $serialized = $this->serializer->serialize($data, 'yml');

                $response->headers->set('Content-Type', sprintf('text/yaml; charset=UTF-8'));
                $response->setContent($serialized);
                break;

            default:
                throw new FileNotFoundException($request->getPathInfo());
        }

        return $response;
    }
}
