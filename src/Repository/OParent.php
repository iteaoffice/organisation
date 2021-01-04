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

use Affiliation\Entity\Affiliation;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Organisation\Entity;
use Program\Entity\Program;
use Project\Entity\Version\Version;

use function in_array;
use function sprintf;

/**
 * Class OParent
 *
 * @package Organisation\Repository
 */
final class OParent extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_parent.type', 'parent_entity_type');

        $queryBuilder->leftJoin('organisation_entity_parent.financial', 'parent_entity_financial');
        $queryBuilder->leftJoin('parent_entity_financial.organisation', 'organisation_entity_financial_organisation');
        $queryBuilder->leftJoin(
            'organisation_entity_financial_organisation.financial',
            'organisation_entity_financial_organisation_financial'
        );
        $queryBuilder->join('organisation_entity_parent.contact', 'contact_entity_contact');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');


        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('organisation_entity_organisation.organisation', ':like'),
                    $queryBuilder->expr()->like('organisation_entity_financial_organisation_financial.vat', ':like')
                )
            );
            $queryBuilder->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('parent_entity_type.id', $filter['type'])
            );
        }

        if (array_key_exists('memberType', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_parent.memberType', $filter['memberType'])
            );
        }

        if (array_key_exists('artemisiaMemberType', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'organisation_entity_parent.artemisiaMemberType',
                    $filter['artemisiaMemberType']
                )
            );
        }

        if (array_key_exists('epossMemberType', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_parent.epossMemberType', $filter['epossMemberType'])
            );
        }

        if (array_key_exists('program', $filter)) {
            $queryBuilder->join('organisation_entity_parent.doa', 'organisation_entity_parent_doa');
            $queryBuilder->join('organisation_entity_parent_doa.program', 'organisation_entity_parent_doa_program');
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_parent_doa_program.id', $filter['program'])
            );
        }

        $direction = Criteria::ASC;
        if (
            isset($filter['direction'])
            && in_array(
                strtoupper($filter['direction']),
                [Criteria::ASC, Criteria::DESC],
                true
            )
        ) {
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
                $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', Criteria::ASC);
        }

        return $queryBuilder;
    }

    public function findActiveParents(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        return $queryBuilder->getQuery()->getResult();
    }

    public function findParentByOrganisationName(string $name): ?Entity\OParent
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));
        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->andWhere('organisation_entity_organisation.organisation = :organisation');
        $queryBuilder->setParameter('organisation', $name);
        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


    public function findActiveParentWithoutFinancial(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');

        //Make a second sub-select to cancel out organisations which have a financial organisation
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('financialParent');
        $subSelect2->from(Entity\Parent\Financial::class, 'organisation_entity_parent_financial');
        $subSelect2->join('organisation_entity_parent_financial.parent', 'financialParent');

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->notIn('organisation_entity_parent', $subSelect2->getDQL())
        );

        $queryBuilder->join('organisation_entity_organisation.affiliation', 'affiliation_entity_affiliation');
        $queryBuilder->join('affiliation_entity_affiliation.project', 'project_entity_project');

        //Include the DOA signers
        $doaSigners = $this->_em->createQueryBuilder();
        $doaSigners->select('doaParent.id');
        $doaSigners->from(Entity\Parent\Doa::class, 'organisation_entity_parent_doa');
        $doaSigners->join('organisation_entity_parent_doa.parent', 'doaParent');


        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq(
                    'organisation_entity_parent.memberType',
                    Entity\OParent::MEMBER_TYPE_MEMBER
                ),
                $queryBuilder->expr()
                    ->notIn(
                        'organisation_entity_parent',
                        $doaSigners->getDQL()
                    )
            )
        );

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->like('organisation_entity_organisation.organisation', ':like')
            );
            $queryBuilder->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        if (array_key_exists('memberType', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_parent.memberType', $filter['memberType'])
            );
        }

        if (array_key_exists('artemisiaMemberType', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'organisation_entity_parent.artemisiaMemberType',
                    $filter['artemisiaMemberType']
                )
            );
        }

        if (array_key_exists('epossMemberType', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_parent.epossMemberType', $filter['epossMemberType'])
            );
        }

        $direction = Criteria::ASC;
        if (
            isset($filter['direction'])
            && in_array(
                strtoupper($filter['direction']),
                [Criteria::ASC, Criteria::DESC],
                true
            )
        ) {
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
            default:
                $queryBuilder->addOrderBy('organisation_entity_organisation.id', $direction);
        }

        return $queryBuilder;
    }

    public function findActiveParentWhichAreNoMember(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');
        $queryBuilder->join('organisation_entity_parent.type', 'organisation_entity_parent_type');

        //Make a second subselect to filter on parents which are active in projects
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('projectParent');
        $subSelect2->from(Affiliation::class, 'affiliation_entity_affiliation');
        $subSelect2->join(
            'affiliation_entity_affiliation.parentOrganisation',
            'organisation_entity_parent_organisation'
        );
        $subSelect2->join('organisation_entity_parent_organisation.parent', 'projectParent');
        $subSelect2->join('affiliation_entity_affiliation.project', 'project_entity_project');
        $subSelect2->join('project_entity_project.call', 'program_entity_call');
        $subSelect2->andWhere($subSelect2->expr()->isNull('affiliation_entity_affiliation.dateEnd'));

        //The project should at least have an approved PO
        //Select projects based on a type
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('activeProject.id');
        $subSelect->from(Version::class, 'activeProjectVersion');
        $subSelect->join('activeProjectVersion.project', 'activeProject');
        $subSelect->where('activeProjectVersion.approved = :approved');
        $subSelect->andWhere('activeProjectVersion.versionType = :versionType');

        $subSelect2->andWhere($queryBuilder->expr()->in('project_entity_project', $subSelect->getDQL()));

        $queryBuilder->setParameter('approved', Version::STATUS_APPROVED);
        $queryBuilder->setParameter('versionType', \Project\Entity\Version\Type::TYPE_PO);


        if (array_key_exists('program', $filter)) {
            $subSelect2->andWhere($queryBuilder->expr()->in('program_entity_call.program', $filter['program']));
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->in('organisation_entity_parent', $subSelect2->getDQL())
        );


        //Exclude the members
        $queryBuilder->andWhere(
            $queryBuilder->expr()->neq('organisation_entity_parent.memberType', Entity\OParent::MEMBER_TYPE_MEMBER)
        );

        //Exclude the DOA signers
        $doaSigners = $this->_em->createQueryBuilder();
        $doaSigners->select('doaParent.id');
        $doaSigners->from(Entity\Parent\Doa::class, 'organisation_entity_parent_doa');
        $doaSigners->join('organisation_entity_parent_doa.parent', 'doaParent');

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->notIn('organisation_entity_parent', $doaSigners->getDQL())
        );


        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->like('organisation_entity_organisation.organisation', ':like')
            );
            $queryBuilder->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()
                    ->in('organisation_entity_organisation.type', implode(', ', $filter['type']))
            );
        }

        $direction = Criteria::ASC;
        if (
            isset($filter['direction'])
            && in_array(
                strtoupper($filter['direction']),
                [Criteria::ASC, Criteria::DESC],
                true
            )
        ) {
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
            default:
                $queryBuilder->addOrderBy('organisation_entity_organisation.id', $direction);
        }

        return $queryBuilder;
    }

    public function findParentsForInvoicing(Program $program): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));


        //Make a second subselect to filter on parents which are active in projects
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('projectParent');
        $subSelect2->from(Affiliation::class, 'affiliation_entity_affiliation');
        $subSelect2->join(
            'affiliation_entity_affiliation.parentOrganisation',
            'organisation_entity_parent_organisation'
        );
        $subSelect2->join('organisation_entity_parent_organisation.parent', 'projectParent');
        $subSelect2->join('affiliation_entity_affiliation.project', 'project_entity_project');
        $subSelect2->join('project_entity_project.call', 'program_entity_call');
        $subSelect2->andWhere('program_entity_call.program = :program');
        $subSelect2->andWhere($subSelect2->expr()->isNull('affiliation_entity_affiliation.dateEnd'));

        //The project should at least have an approved FPP
        //Select projects based on a type
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('activeProject.id');
        $subSelect->from(Version::class, 'activeProjectVersion');
        $subSelect->join('activeProjectVersion.project', 'activeProject');
        $subSelect->where('activeProjectVersion.approved = :approved');
        $subSelect->andWhere('activeProjectVersion.versionType = :versionType');

        $subSelect2->andWhere($queryBuilder->expr()->in('project_entity_project', $subSelect->getDQL()));

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->in('organisation_entity_parent', $subSelect2->getDQL())
        );


        $queryBuilder->setParameter('approved', Version::STATUS_APPROVED);
        $queryBuilder->setParameter('versionType', \Project\Entity\Version\Type::TYPE_FPP);
        $queryBuilder->setParameter('program', $program);

        $queryBuilder->addOrderBy('organisation_entity_organisation.organisation');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findParentsForExtraInvoicing(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_parent');
        $queryBuilder->from(Entity\OParent::class, 'organisation_entity_parent');
        $queryBuilder->join('organisation_entity_parent.organisation', 'organisation_entity_organisation');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.dateEnd'));

        $queryBuilder = $this->limitCChambers($queryBuilder);

        $queryBuilder->addOrderBy('organisation_entity_organisation.organisation');

        return $queryBuilder->getQuery()->getResult();
    }

    public function limitCChambers(QueryBuilder $queryBuilder): QueryBuilder
    {
        //Select projects based on a type
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_parent_c_chamber.id');
        $subSelect->from(Entity\OParent::class, 'organisation_entity_parent_c_chamber');
        $subSelect->join('organisation_entity_parent_c_chamber.type', 'organisation_entity_parent_c_chamber_type');

        $subSelect->andWhere('organisation_entity_parent_c_chamber_type.id = :type');
        $subSelect->andWhere('organisation_entity_parent_c_chamber.memberType = :memberType');

        $queryBuilder->setParameter('type', Entity\Parent\Type::TYPE_C_CHAMBER);
        $queryBuilder->setParameter('memberType', Entity\OParent::MEMBER_TYPE_MEMBER);

        $queryBuilder->andWhere($queryBuilder->expr()->in('organisation_entity_parent', $subSelect->getDQL()));

        return $queryBuilder;
    }

    public function limitFreeRiders(QueryBuilder $queryBuilder, Program $program): QueryBuilder
    {
        //Limit parents based on membership
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_parent_freerider.id');
        $subSelect->from(Entity\OParent::class, 'organisation_entity_parent_freerider');

        $subSelect->andWhere('organisation_entity_parent_freerider.memberType = :memberType');
        $subSelect->andWhere('organisation_entity_parent_freerider.epossMemberType = :epossMemberType');
        $subSelect->andWhere('organisation_entity_parent_freerider.artemisiaMemberType = :artemisiaMemberType');

        $queryBuilder->setParameter('memberType', Entity\OParent::MEMBER_TYPE_NO_MEMBER);
        $queryBuilder->setParameter('epossMemberType', Entity\OParent::EPOSS_MEMBER_TYPE_NO_MEMBER);
        $queryBuilder->setParameter('artemisiaMemberType', Entity\OParent::ARTEMISIA_MEMBER_TYPE_NO_MEMBER);

        //Limit parents based on the signed DOA
        $doaSubselect = $this->_em->createQueryBuilder();
        $doaSubselect->select('organisation_entity_parent_doa');
        $doaSubselect->from(Entity\OParent::class, 'organisation_entity_parent_doa');
        $doaSubselect->join('organisation_entity_parent_doa.doa', 'organisation_entity_parent_doa_doa');
        $doaSubselect->andWhere('organisation_entity_parent_doa_doa.program = :program');

        $queryBuilder->setParameter('program', $program);

        $queryBuilder->andWhere($queryBuilder->expr()->in('organisation_entity_parent', $subSelect->getDQL()));
        $queryBuilder->andWhere($queryBuilder->expr()->notIn('organisation_entity_parent', $doaSubselect->getDQL()));

        return $queryBuilder;
    }

    public function searchParents(
        string $searchItem,
        int $maxResults
    ): array {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            [
                'organisation_entity_parent.id',
                'organisation_entity_organisation.organisation',
                'general_entity_country.iso3',
            ]
        );
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');
        //Do a full inner join on parent, so we only have parents
        $queryBuilder->join('organisation_entity_organisation.parent', 'organisation_entity_parent');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('organisation_entity_organisation.organisation', ':like'),
                $queryBuilder->expr()->like('general_entity_country.country', ':like'),
                $queryBuilder->expr()->like('general_entity_country.iso3', ':like')
            )
        );
        $queryBuilder->setParameter('like', sprintf('%%%s%%', $searchItem));

        $queryBuilder->setMaxResults($maxResults);
        $queryBuilder->orderBy('organisation_entity_organisation.organisation', Criteria::ASC);
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
