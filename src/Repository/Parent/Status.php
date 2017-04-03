<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Repository\Parent;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Organisation\Entity;

/**
 * @category    Member
 */
class Status extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent_status');
        $queryBuilder->from(Entity\Parent\Status::class, 'organisation_entity_parent_status');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('organisation_entity_parent_status.status', ':like'));
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'status':
                $queryBuilder->addOrderBy('organisation_entity_parent_status.status', $direction);
                break;
            case 'description':
                $queryBuilder->addOrderBy('organisation_entity_parent_status.description', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_parent_status.id', $direction);
        }

        return $queryBuilder->getQuery();
    }
}
