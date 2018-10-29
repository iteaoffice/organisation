<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Financial
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Organisation\Entity;

/**
 * Class Financial
 *
 * @package Organisation\Repository
 */
final class Financial extends EntityRepository
{
    public function findOrganisationFinancialList(array $filter): Query
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('financial', 'organisation', 'country');
        $queryBuilder->from(Entity\Financial::class, 'financial');
        $queryBuilder->join('financial.organisation', 'organisation');
        $queryBuilder->join('organisation.country', 'country');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('organisation.organisation', ':like'),
                    $queryBuilder->expr()->like('financial.vat', ':like')
                )
            );
            $queryBuilder->setParameter(
                'like',
                sprintf("%%%s%%", $filter['search'])
            );
        }

        if (array_key_exists('vatStatus', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in(
                        'financial.vatStatus',
                        implode($filter['vatStatus'], ', ')
                    )
            );
        }

        if (array_key_exists('omitContact', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in(
                        'financial.omitContact',
                        implode($filter['omitContact'], ', ')
                    )
            );
        }

        if (array_key_exists('requiredPurchaseOrder', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in(
                        'financial.requiredPurchaseOrder',
                        implode($filter['requiredPurchaseOrder'], ', ')
                    )
            );
        }

        if (array_key_exists('email', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in('financial.email', implode($filter['email'], ', '))
            );
        }

        $direction = 'ASC';
        if (isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy(
                    'organisation.organisation',
                    $direction
                );
                break;
            default:
                $queryBuilder->addOrderBy('organisation.organisation', $direction);
        }

        return $queryBuilder->getQuery();
    }
}
