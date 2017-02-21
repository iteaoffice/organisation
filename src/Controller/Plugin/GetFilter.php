<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Application
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\Controller\Plugin;

/**
 * @category    Application
 */
class GetFilter extends AbstractOrganisationPlugin
{
    /**
     * @var array
     */
    protected $filter = [];

    /**
     * Instantiate the filter
     *
     * @return GetFilter
     */
    public function __invoke()
    {
        $encodedFilter = urldecode($this->getRouteMatch()->getParam('encodedFilter'));

        $order = $this->getRequest()->getQuery('order');
        $direction = $this->getRequest()->getQuery('direction');

        //Take the filter from the URL
        $filter = unserialize(base64_decode($encodedFilter));

        //If the form is submitted, refresh the URL
        if ($this->getRequest()->isGet() && !is_null($this->getRequest()->getQuery('submit'))) {
            $filter = $this->getRequest()->getQuery()->toArray()['filter'];
        }

        //Create a new filter if not set already
        if (!$filter) {
            $filter = [];
        }

        //Add a default order and direction if not known in the filter
        if (!isset($filter['order'])) {
            $filter['order'] = 'name';
            $filter['direction'] = 'asc';
        }

        //Overrule the order if set in the query
        if (!is_null($order)) {
            $filter['order'] = $order;
        }

        //Overrule the direction if set in the query
        if (!is_null($direction)) {
            $filter['direction'] = $direction;
        }

        $this->filter = $filter;

        return $this;
    }


    /**
     * Return the filter
     *
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->filter['order'];
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->filter['direction'];
    }

    /**
     * Give the compressed version of the filter
     *
     * @return string
     */
    public function getHash()
    {
        return base64_encode(serialize($this->filter));
    }
}
