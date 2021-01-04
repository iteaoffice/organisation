<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Organisation\Entity;

use function in_array;

/**
 * Class Type
 *
 * @package Organisation\Repository
 */
final class Type extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_type');
        $queryBuilder->from(Entity\Type::class, 'organisation_entity_type');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('organisation_entity_type.type', ':like'));
            $queryBuilder->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'type':
                $queryBuilder->addOrderBy('organisation_entity_type.type', $direction);
                break;
            case 'description':
                $queryBuilder->addOrderBy('organisation_entity_type.description', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_type.type', $direction);
        }

        return $queryBuilder;
    }
}
