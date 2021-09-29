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
class TenderSearchService extends AbstractSearchService
{
    public const SOLR_CONNECTION = 'organisation_advisory_board_tender';

    public function setSearch(
        string $searchTerm,
        array $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface {
        $this->setQuery($this->getSolrClient()->createSelect());

        $eb          = new ExpressionBuilder();
        $searchQuery = $eb->all(
            $eb->comp(
                [
                    $eb->field('title', $eb->boost($searchTerm, 200)),
                    $eb->field('type', $eb->boost($searchTerm, 50)),
                    $eb->field('city', $eb->boost($searchTerm, 50)),
                    $eb->field('country', $eb->boost($searchTerm, 50)),
                    $eb->field('contact', $eb->boost($searchTerm, 50)),

                    $eb->field('title_search', $eb->wild($searchTerm)),
                    $eb->field('description_search', $eb->wild($searchTerm)),
                    $eb->field('city_search', $eb->wild($searchTerm)),
                    $eb->field('country_search', $eb->wild($searchTerm)),
                    $eb->field('contact_search', $eb->wild($searchTerm)),
                ],
                CompositeExpression::TYPE_OR
            )
        );

        $this->getQuery()->setQuery((string)$searchQuery);

        $hasTerm = ! \in_array($searchTerm, ['*', ''], true);

        if (! $hasTerm) {
            switch ($order) {
                case 'title':
                    $this->getQuery()->addSort('title_sort', $direction);
                    break;
                case 'contact':
                    $this->getQuery()->addSort('contact_sort', $direction);
                    break;
                case 'city':
                    $this->getQuery()->addSort('city_sort', $direction);
                    break;
                case 'country':
                    $this->getQuery()->addSort('country_sort', $direction);
                    break;
                case 'language':
                    $this->getQuery()->addSort('language_sort', $direction);
                    break;
                case 'date_created':
                    $this->getQuery()->addSort('date_created', $direction);
                    break;
                case 'date_updated':
                    $this->getQuery()->addSort('date_updated', $direction);
                    break;
                default:
                    $this->getQuery()->addSort('date_created', Query::SORT_DESC);
                    break;
            }
        }


        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('type')->setField('type')->setSort('index')
            ->setMinCount(1)->setExcludes(['type']);
        $facetSet->createFacetField('language')->setField('type')->setSort('index')
            ->setMinCount(1)->setExcludes(['language']);
        $facetSet->createFacetField('country')->setField('country')->setSort('index')
            ->setMinCount(1)->setExcludes(['country']);

        return $this;
    }
}
