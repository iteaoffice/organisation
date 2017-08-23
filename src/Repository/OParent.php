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

declare(strict_types=1);

namespace Organisation\Repository;

use Affiliation\Entity\Affiliation;
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
    public function findFiltered(array $filter): Query
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
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
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
    public function findActiveParents(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $filter
     * @return Query
     */
    public function findActiveParentWithoutFinancial(array $filter): Query
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');
        $queryBuilder->join('organisation_entity_parent.status', 'organisation_entity_parent_status');
        $queryBuilder->join('organisation_entity_parent.type', 'organisation_entity_parent_type');

        //Make a second sub-select to cancel out organisations which have a financial organisation
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('financialParent');
        $subSelect2->from(Entity\Parent\Financial::class, 'organisation_entity_parent_financial');
        $subSelect2->join('organisation_entity_parent_financial.parent', 'financialParent');

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->notIn('organisation_entity_parent', $subSelect2->getDQL())
        );

        //Exclude the free-riders
        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->notIn('organisation_entity_parent_status.id', [Entity\Parent\Status::STATUS_FREE_RIDER])
        );

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->like('organisation_entity_organisation.organisation', ':like')
            );
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in('organisation_entity_organisation.type', $filter['type'])
            );
        }

        if (array_key_exists('status', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in('organisation_entity_organisation.type', $filter['status'])
            );
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'lastUpdate':
                $queryBuilder->addOrderBy('organisation_entity_organisation.lastUpdate', $direction);
                break;
            case 'name':
                $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', $direction);
                break;
            case 'country':
                $queryBuilder->addOrderBy('general_entity_country.iso3', $direction);
                break;
            case 'type':
                $queryBuilder->addOrderBy('organisation_entity_parent_type.type', $direction);
                break;
            case 'status':
                $queryBuilder->addOrderBy('organisation_entity_parent_status.status', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_organisation.id', $direction);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param array $filter
     * @return Query
     */
    public function findActiveParentWhichAreNoMember(array $filter): Query
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');
        $queryBuilder->join('organisation_entity_parent.status', 'organisation_entity_parent_status');
        $queryBuilder->join('organisation_entity_parent.type', 'organisation_entity_parent_type');

        //Make a second sub-select to cancel out organisations which have a financial organisation
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('projectParent');
        $subSelect2->from(Affiliation::class, 'affiliation_entity_affiliation');
        $subSelect2->join('affiliation_entity_affiliation.parentOrganisation', 'organisation_entity_parent_organisation');
        $subSelect2->join('organisation_entity_parent_organisation.parent', 'projectParent');

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->in('organisation_entity_parent', $subSelect2->getDQL())
        );

        //Exclude the free-riders
        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->notIn('organisation_entity_parent_status.id', [Entity\Parent\Status::STATUS_MEMBER])
        );

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->like('organisation_entity_organisation.organisation', ':like')
            );
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in('organisation_entity_organisation.type', implode($filter['type'], ', '))
            );
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'lastUpdate':
                $queryBuilder->addOrderBy('organisation_entity_organisation.lastUpdate', $direction);
                break;
            case 'name':
                $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', $direction);
                break;
            case 'country':
                $queryBuilder->addOrderBy('general_entity_country.iso3', $direction);
                break;
            case 'type':
                $queryBuilder->addOrderBy('organisation_entity_parent_type.type', $direction);
                break;
            case 'status':
                $queryBuilder->addOrderBy('organisation_entity_parent_status.status', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_organisation.id', $direction);
        }

        return $queryBuilder->getQuery();
    }


    /**
     * @return array
     */
    public function findParentsForInvoicing(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        //Limit on parent types which have a fee
        $queryBuilder->join('organisation_entity_parent.status', 'organisation_entity_parent_status');
        $queryBuilder->join('organisation_entity_parent_status.projectFee', 'project_entity_fee');

        $queryBuilder->addOrderBy('organisation_entity_parent.status', 'ASC');
        $queryBuilder->addOrderBy('organisation_entity_parent.type', 'DESC');


        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findParentsForExtraInvoicing(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        $queryBuilder = $this->limitCChambers($queryBuilder);

        $queryBuilder->addOrderBy('organisation_entity_parent.status', 'ASC');
        $queryBuilder->addOrderBy('organisation_entity_parent.type', 'DESC');


        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * This subselect returns all free riders and limits the query automatically
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    public function limitFreeRiders(QueryBuilder $queryBuilder): QueryBuilder
    {
        //Select projects based on a type
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_parent_freerider.id');
        $subSelect->from(Entity\OParent::class, 'organisation_entity_parent_freerider');
        $subSelect->join('organisation_entity_parent_freerider.status', 'organisation_entity_parent_freerider_status');

        $subSelect->andWhere('organisation_entity_parent_freerider_status.id = :status');
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
    public function limitCChambers(QueryBuilder $queryBuilder): QueryBuilder
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
