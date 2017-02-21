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
 * @link        http://github.com/iteaoffice/parent for the canonical source repository
 */

namespace Organisation\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Organisation\Entity;

/**
 * @category    Parent
 */
class OParent extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_parent.type', 'parent_entity_type');
        $queryBuilder->leftJoin('organisation_entity_parent.status', 'parent_entity_status');
        $queryBuilder->join('organisation_entity_parent.contact', 'contact_entity_contact');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');


        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->like('organisation_entity_organisation.organisation', ':like')
            );
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('parent_entity_type.id', $filter['type'])
            );
        }

        if (array_key_exists('status', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('parent_entity_status.id', $filter['status'])
            );
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', $direction);
                break;
            case 'country':
                $queryBuilder->addOrderBy('general_entity_country.iso3', $direction);
                break;
            case 'contact':
                $queryBuilder->addOrderBy('contact_entity_contact.lastName', $direction);
                break;
            case 'type':
                $queryBuilder->addOrderBy('parent_entity_type.type', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_parent.id', $direction);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @return array
     */
    public function findActiveParents()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findParentsForInvoicing()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        //Limit on parent types which have a fee
        $queryBuilder->join('organisation_entity_parent.status', 'organisation_entity_parent_status');
        $queryBuilder->join('organisation_entity_parent_status.projectFee', 'project_entity_fee');

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * This subselect returns all free riders and limits the query automatically
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    public function limitFreeRiders(QueryBuilder $queryBuilder)
    {
        //Select projects based on a type
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_parent_freerider.id');
        $subSelect->from(Entity\OParent::class, 'organisation_entity_parent_freerider');
        $subSelect->join('organisation_entity_parent_freerider.status', 'organisation_entity_parent_freerider_status');

        $subSelect->andWhere('organisation_entity_parent_freerider_status.status = :status');
        $subSelect->andWhere('organisation_entity_parent_freerider.epossMemberType = :epossMemberType');
        $subSelect->andWhere('organisation_entity_parent_freerider.artemisiaMemberType = :artemisiaMemberType');

        $queryBuilder->setParameter('status', Entity\Parent\Status::STATUS_FREE_RIDER);
        $queryBuilder->setParameter('epossMemberType', Entity\OParent::EPOSS_MEMBER_TYPE_NO_MEMBER);
        $queryBuilder->setParameter('artemisiaMemberType', Entity\OParent::ARTEMISIA_MEMBER_TYPE_NO_MEMBER);

        $queryBuilder->andWhere($queryBuilder->expr()->in('organisation_entity_parent', $subSelect->getDQL()));

        return $queryBuilder;
    }

    /**
     * This subselect returns all free riders and limits the query automatically
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    public function limitCChambers(QueryBuilder $queryBuilder)
    {
        //Select projects based on a type
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_parent_c_chamber.id');
        $subSelect->from(Entity\OParent::class, 'organisation_entity_parent_c_chamber');
        $subSelect->join('organisation_entity_parent_c_chamber.type', 'organisation_entity_parent_c_chamber_type');
        $subSelect->join('organisation_entity_parent_c_chamber.status', 'organisation_entity_parent_c_chamber_status');

        $subSelect->andWhere('organisation_entity_parent_c_chamber_type.id = :type');
        $subSelect->andWhere('organisation_entity_parent_c_chamber_status.id = :status');

        $queryBuilder->setParameter('type', Entity\Parent\Type::TYPE_C_CHAMBER);
        $queryBuilder->setParameter('status', Entity\Parent\Status::STATUS_MEMBER);

        $queryBuilder->andWhere($queryBuilder->expr()->in('organisation_entity_parent', $subSelect->getDQL()));

        return $queryBuilder;
    }
}
