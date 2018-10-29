<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Doctrine\Common\Collections\Criteria;
use Zend\Http\Request;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;

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
        $encodedFilter = \urldecode((string)$application->getMvcEvent()->getRouteMatch()->getParam('encodedFilter'));
        /** @var Request $request */
        $request = $application->getMvcEvent()->getRequest();

        if (!empty($encodedFilter)) {
            // Take the filter from the URL
            $filter = (array)\json_decode(\base64_decode($encodedFilter));
        }

        $order = $request->getQuery('order');
        $direction = $request->getQuery('direction');

        // If the form is submitted, refresh the URL
        if ($request->isGet()
            && (($request->getQuery('submit') !== null) || ($request->getQuery('presentation') !== null))
        ) {
            $query = $request->getQuery()->toArray();
            if (isset($query['filter'])) {
                $filter = $query['filter'];
            }
        }

        // Add a default order and direction if not known in the filter
        if (!isset($filter['order'])) {
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

        return \http_build_query(['filter' => $filterCopy, 'submit' => 'true']);
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
        return \base64_encode(\json_encode($this->filter));
    }
}
