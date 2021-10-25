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

use Organisation\Entity\AdvisoryBoard\Solution;
use Search\Service\AbstractSearchService;
use Search\Service\SearchServiceInterface;
use Search\Solr\Expression\CompositeExpression;
use Search\Solr\Expression\ExpressionBuilder;
use Solarium\QueryType\Select\Query\Query;

use function in_array;

/**
 * Class OrganisationSearchService
 *
 * @package Organisation\Search\Service
 */
class SolutionSearchService extends AbstractSearchService
{
    public const SOLR_CONNECTION = 'organisation_advisory_board_solution';

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
                    $eb->field('name', $eb->boost($searchTerm, 200)),

                    $eb->field('name_search', $eb->wild($searchTerm)),
                    $eb->field('target_customers_search', $eb->wild($searchTerm)),
                ],
                CompositeExpression::TYPE_OR
            )
        );

        $this->getQuery()->setQuery((string)$searchQuery);

        $hasTerm = ! in_array($searchTerm, ['*', ''], true);

        if (! $hasTerm) {
            switch ($order) {
                case 'title':
                    $this->getQuery()->addSort('title_sort', $direction);
                    break;
                case 'contact':
                    $this->getQuery()->addSort('contact_sort', $direction);
                    break;
                case 'date_created':
                    $this->getQuery()->addSort('date_created', $direction);
                    break;
                default:
                    $this->getQuery()->addSort('date_created', Query::SORT_DESC);
                    break;
            }
        }


        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        }

        return $this;
    }

    public function setPublicSearch(string $searchTerm): SearchServiceInterface
    {
        $this->setQuery($this->getSolrClient()->createSelect());

        $eb          = new ExpressionBuilder();
        $searchQuery = $eb->all(
            $eb->comp(
                [
                    $eb->field('name', $eb->boost($searchTerm, 200)),

                    $eb->field('name_search', $eb->wild($searchTerm)),
                    $eb->field('target_customers_search', $eb->wild($searchTerm)),
                ],
                CompositeExpression::TYPE_OR
            )
        );

        $this->getQuery()->setQuery((string)$searchQuery);

//        //We do not want any hidden
        $filterQuery = $eb->comp(
            [
                $eb->field('is_hidden', false)
            ],
            CompositeExpression::TYPE_AND
        );

        $this->getQuery()->addFilterQuery(
            [
                'key'   => 'no_hidden',
                'query' => (string)$filterQuery,
            ]
        );

        $hasTerm = ! in_array($searchTerm, ['*', ''], true);

        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        } else {
            $this->getQuery()->addSort('title_sort', Query::SORT_ASC);
        }

        return $this;
    }
}
