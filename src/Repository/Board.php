<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Organisation\Entity;

use function in_array;

/**
 * Class Board
 * @package Organisation\Repository
 */
final class Board extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('organisation_entity_board');
        $qb->from(Entity\Board::class, 'organisation_entity_board');
        $qb->join('organisation_entity_board.cluster', 'cluster_entity_cluster');
        $qb->join('organisation_entity_board.organisation', 'organisation_entity_organisation');

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('cluster_entity_cluster.name', ':like'),
                    $qb->expr()->like('cluster_entity_cluster.description', ':like'),
                    $qb->expr()->like('organisation_entity_organisation.organisation', ':like'),
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = Criteria::ASC;
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'organisation':
                $qb->addOrderBy('organisation_entity_organisation.organisation', $direction);
                break;
            case 'cluster':
                $qb->addOrderBy('cluster_entity_cluster.cluster', $direction);
                break;
            case 'date-signed':
                $qb->addOrderBy('organisation_entity_board.dateSigned', $direction);
                break;
            case 'date-end':
                $qb->addOrderBy('organisation_entity_board.dateEnd', $direction);
                break;
            default:
                $qb->addOrderBy('organisation_entity_board.dateSigned', $direction);
        }

        return $qb;
    }
}
