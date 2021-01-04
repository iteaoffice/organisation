<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Doctrine\Common\Collections\Criteria;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Mvc\Application;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceManager;

use function base64_decode;
use function base64_encode;
use function http_build_query;
use function json_encode;
use function urldecode;

/**
 * Class GetFilter
 *
 * @package Organisation\Controller\Plugin
 */
final class GetFilter extends AbstractPlugin
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;
    /**
     * @var array
     */
    private $filter = [];

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function __invoke(): GetFilter
    {
        $filter = [];
        /** @var Application $application */
        $application = $this->serviceManager->get('application');
        $encodedFilter = urldecode((string)$application->getMvcEvent()->getRouteMatch()->getParam('encodedFilter'));
        /** @var Request $request */
        $request = $application->getMvcEvent()->getRequest();

        $filter = [];
        if (! empty($base64decodedFilter = base64_decode($encodedFilter))) {
            $filter = (array)Json::decode($base64decodedFilter);
        }

        $order = $request->getQuery('order');
        $direction = $request->getQuery('direction');

        // If the form is submitted, refresh the URL
        if (
            $request->isGet()
            && (($request->getQuery('submit') !== null) || ($request->getQuery('presentation') !== null))
        ) {
            $query = $request->getQuery()->toArray();
            if (isset($query['filter'])) {
                $filter = $query['filter'];
            }
        }

        // Add a default order and direction if not known in the filter
        if (! isset($filter['order'])) {
            $filter['order'] = '';
            $filter['direction'] = Criteria::ASC;
        }

        // Overrule the order if set in the query
        if (null !== $order) {
            $filter['order'] = $order;
        }

        // Overrule the direction if set in the query
        if (null !== $direction) {
            $filter['direction'] = $direction;
        }

        $this->filter = $filter;

        return $this;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function parseFilteredSortQuery(array $removeParams = []): string
    {
        $filterCopy = $this->filter;
        unset($filterCopy['order'], $filterCopy['direction']);
        foreach ($removeParams as $param) {
            unset($filterCopy[$param]);
        }

        return http_build_query(['filter' => $filterCopy, 'submit' => 'true']);
    }

    public function getOrder(): string
    {
        return $this->filter['order'];
    }

    public function getDirection(): string
    {
        return $this->filter['direction'];
    }

    public function getHash(): string
    {
        return base64_encode(json_encode($this->filter));
    }
}
