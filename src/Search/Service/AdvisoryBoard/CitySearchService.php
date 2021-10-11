<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Search\Service\AdvisoryBoard;

use Search\Service\AbstractSearchService;
use Search\Service\SearchServiceInterface;
use Search\Solr\Expression\CompositeExpression;
use Search\Solr\Expression\ExpressionBuilder;
use Solarium\QueryType\Select\Query\Query;

/**
 * Class OrganisationSearchService
 *
 * @package Organisation\Search\Service
 */
class CitySearchService extends AbstractSearchService
{
    public const SOLR_CONNECTION = 'organisation_advisory_board_city';

    public function setSearch(
        string $searchTerm,
        array  $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface
    {
        $this->setQuery($this->getSolrClient()->createSelect());

        $eb          = new ExpressionBuilder();
        $searchQuery = $eb->all(
            $eb->comp(
                [
                    $eb->field('name', $eb->boost($searchTerm, 200)),
                    $eb->field('country', $eb->boost($searchTerm, 50)),

                    $eb->field('name_search', $eb->wild($searchTerm)),
                    $eb->field('country_search', $eb->wild($searchTerm))
                ],
                CompositeExpression::TYPE_OR
            )
        );

        $this->getQuery()->setQuery((string)$searchQuery);

        $hasTerm = !\in_array($searchTerm, ['*', ''], true);

        if (!$hasTerm) {
            switch ($order) {
                case 'name':
                    $this->getQuery()->addSort('name_sort', $direction);
                    break;
                case 'contact':
                    $this->getQuery()->addSort('contact_sort', $direction);
                    break;
                case 'country':
                    $this->getQuery()->addSort('country_sort', $direction);
                    break;
                default:
                    $this->getQuery()->addSort('name_sort', Query::SORT_ASC);
                    break;
            }
        }


        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('country')->setField('country')->setSort('index')
            ->setMinCount(1)->setExcludes(['country']);

        return $this;
    }
}
