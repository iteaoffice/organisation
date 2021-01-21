<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace organisation\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Organisation\Entity;

use function array_key_exists;
use function in_array;

/**
 * Class SelectionRepository
 * @package organisation\Repository
 */
class SelectionRepository extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('organisation_entity_selection');
        $qb->from(Entity\Selection::class, 'organisation_entity_selection');

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('organisation_entity_selection.selection', ':like'),
                    $qb->expr()->like('organisation_entity_selection.description', ':like'),
                    $qb->expr()->like('organisation_entity_selection.tag', ':like')
                )
            );

            $qb->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('tags', $filter)) {
            $qb->andWhere($qb->expr()->in('organisation_entity_selection.tag', $filter['tags']));
        }

        $direction = Criteria::ASC;
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $qb->addOrderBy('organisation_entity_selection.selection', $direction);
                break;
            case 'tag':
                $qb->addOrderBy('organisation_entity_selection.tag', $direction);
                break;
            case 'date':
                $qb->addOrderBy('organisation_entity_selection.dateCreated', $direction);
                break;
            default:
                $qb->addOrderBy('organisation_entity_selection.id', $direction);
        }

        return $qb;
    }

    public function findTags(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('organisation_entity_selection.tag');
        $qb->distinct();
        $qb->from(Entity\Selection::class, 'organisation_entity_selection');
        $qb->orderBy('organisation_entity_selection.tag', Criteria::ASC);

        $qb->andWhere($qb->expr()->isNull('organisation_entity_selection.dateDeleted'));

        return $qb->getQuery()->getArrayResult();
    }

    public function findAmountOfOrganisationsInSelection(Entity\Selection $selection): int
    {
        if (! $selection->hasSql()) {
            return 0;
        }

        $resultSetMap = new ResultSetMapping();
        $resultSetMap->addEntityResult(Entity\Organisation::class, 'organisation');
        $resultSetMap->addFieldResult('organisation', 'blabla', 'blabla');

        $query = sprintf(
            'SELECT COUNT(organisation.organisation_id) FROM organisation
            WHERE organisation_id IN (%s)',
            $selection->getSql()
        );

        $statement = $this->_em->getConnection()->prepare($query);
        $statement->execute();

        return (int)$statement->fetchOne();
    }
}
