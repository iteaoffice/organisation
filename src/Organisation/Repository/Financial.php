<?php
/**
 * DebraNova copyright message placeholder.
 *
 * @category    Financial
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Organisation\Entity;

/**
 * @category    Financial
 */
class Financial extends EntityRepository
{
    /**
     * @param array $filter
     * @return Query
     */
    public function findOrganisationFinancialList(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('financial');
        $queryBuilder->from('Organisation\Entity\Financial', 'financial');
        $queryBuilder->join('financial.organisation', 'organisation');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('organisation.organisation', ':like'));
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('vatStatus', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in(
                'financial.vatStatus',
                implode($filter['vatStatus'], ', ')
            ));
        }

        if (array_key_exists('omitContact', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in(
                'financial.omitContact',
                implode($filter['omitContact'], ', ')
            ));
        }

        if (array_key_exists('requiredPurchaseOrder', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in(
                'financial.requiredPurchaseOrder',
                implode($filter['requiredPurchaseOrder'], ', ')
            ));
        }

        if (array_key_exists('email', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in(
                'financial.email',
                implode($filter['email'], ', ')
            ));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy('organisation.organisation', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('financial.id', $direction);

        }

        return $queryBuilder->getQuery();
    }
}
