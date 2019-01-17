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
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Search\Service;

use Search\Service\AbstractSearchService;
use Search\Service\SearchServiceInterface;
use Solarium\QueryType\Select\Query\Query;

/**
 * Class OrganisationSearchService
 *
 * @package Organisation\Search\Service
 */
class OrganisationSearchService extends AbstractSearchService
{
    public const SOLR_CONNECTION = 'organisation_organisation';

    public function setSearch(
        string $searchTerm,
        array $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface {
        $this->setQuery($this->getSolrClient()->createSelect());
        $this->getQuery()->setQuery(static::parseQuery($searchTerm, $searchFields));

        $hasTerm = !\in_array($searchTerm, ['*', ''], true);
        $hasSort = ($order !== '');

        if ($hasSort) {
            switch ($order) {
                default:
                    $this->getQuery()->addSort('organisation', Query::SORT_DESC);
                    break;
            }
        }

        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        } else {
            $this->getQuery()->addSort('organisation', Query::SORT_DESC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('organisation_type')->setField('organisation_type')->setSort('organisation_type')
            ->setMinCount(1)->setExcludes(['organisation_type']);
        $facetSet->createFacetField('country')->setField('country')->setSort('country')->setMinCount(1)->setExcludes(
            ['country']
        );

        return $this;
    }

    public function setSearchForWebsite(
        string $searchTerm,
        array $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface {
        $this->setQuery($this->getSolrClient()->createSelect());

        $query = '(has_projects:true) AND (' . static::parseQuery($searchTerm, $searchFields) . ')';

        $this->getQuery()->setQuery($query);

        $hasTerm = !\in_array($searchTerm, ['*', ''], true);
        $hasSort = ($order !== '');

        if ($hasSort) {
            switch ($order) {
                case 'name':
                    $this->getQuery()->addSort('organisation_sort', $direction);
                    break;
                case 'country':
                    $this->getQuery()->addSort('country_sort', $direction);
                    break;
                case 'type':
                    $this->getQuery()->addSort('organisation_type_sort', $direction);
                    break;
                case 'projects':
                    $this->getQuery()->addSort('projects', $direction);
                    break;
                default:
                    $this->getQuery()->addSort('organisation_sort', Query::SORT_ASC);
                    break;
            }
        }



        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        } else {
            $this->getQuery()->addSort('organisation_sort', Query::SORT_ASC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('organisation_type')->setField('organisation_type')->setSort('organisation_type')
            ->setMinCount(1)->setExcludes(['organisation_type']);
        $facetSet->createFacetField('country')->setField('country')->setSort('country')->setMinCount(1)->setExcludes(
            ['country']
        );

        return $this;
    }

    public function findAmountOfActiveOrganisations(): int
    {
        $this->setQuery($this->getSolrClient()->createSelect());

        $query = 'has_projects:true';
        $this->getQuery()->setQuery($query);


        $result = $this->getSolrClient()->execute($this->query);

        return (int) ($result->getData()['response']['numFound'] ?? 0);
    }
}
