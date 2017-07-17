<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Repository;

use Affiliation\Entity\Affiliation;
use Contact\Entity\Contact;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Event\Entity\Meeting\Meeting;
use Event\Entity\Registration;
use General\Entity\Country;
use Organisation\Entity;
use Project\Repository\Project;
use Zend\Stdlib\Parameters;
use Zend\Validator\EmailAddress;

/**
 * @category    Organisation
 */
class Organisation extends EntityRepository
{
    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findFiltered(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select([
            'organisation_entity_organisation',
            'general_entity_country',
            'organisation_entity_type'
        ]);
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');
        $queryBuilder->join('organisation_entity_organisation.type', 'organisation_entity_type');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('organisation_entity_organisation.organisation', ':like')
            );
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_organisation.type', implode($filter['type'], ', '))
            );
        }

        if (array_key_exists('options', $filter) && in_array('1', $filter['options'], true)) {
            //Make a second sub-select to cancel out organisations which have a financial organisation
            $subSelect2 = $this->_em->createQueryBuilder();
            $subSelect2->select('affiliation_entity_affiliation_organisation');
            $subSelect2->from(Affiliation::class, 'affiliation_entity_affiliation');
            $subSelect2->andWhere($queryBuilder->expr()->isNull('affiliation_entity_affiliation.dateEnd'));
            $subSelect2->innerJoin(
                'affiliation_entity_affiliation.organisation',
                'affiliation_entity_affiliation_organisation'
            );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('organisation_entity_organisation.id', $subSelect2->getDQL())
            );
        }

        if (array_key_exists('options', $filter) && in_array('2', $filter['options'], true)) {
            //Make a second sub-select to cancel out organisations which have a financial organisation
            $queryBuilder->join('organisation_entity_organisation.parent', 'parent');
        }

        $direction = Criteria::ASC;
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)) {
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
                $queryBuilder->addOrderBy('general_entity_country.country', $direction);
                break;
            case 'type':
                $queryBuilder->addOrderBy('organisation_entity_type.type', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_organisation.id', $direction);
        }

        return $queryBuilder->getQuery();
    }


    /**
     * @param array $filter
     *
     * @return Query
     */
    public function findActiveOrganisationWithoutFinancial(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');

        //Make a second sub-select to cancel out organisations which have a financial organisation
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('financialOrganisation');
        $subSelect2->from(Entity\Financial::class, 'organisation_entity_financial');
        $subSelect2->join('organisation_entity_financial.organisation', 'financialOrganisation');

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->notIn('organisation_entity_organisation', $subSelect2->getDQL())
        );

        $queryBuilder->join('organisation_entity_organisation.affiliation', 'affiliation_entity_affiliation');
        $queryBuilder->join('affiliation_entity_affiliation.project', 'project_entity_project');

        //Limit to only the active projects

        /** @var Project $projectRepository */
        $projectRepository = $this->getEntityManager()->getRepository(\Project\Entity\Project::class);
        $queryBuilder = $projectRepository->onlyActiveProject($queryBuilder);

        //Limit to projects which are not recently completed
        $queryBuilder->andWhere('project_entity_project.dateEndActual > :lastYear');

        $nextYear = new \DateTime();
        $nextYear->sub(new \DateInterval('P1Y'));

        $queryBuilder->setParameter('lastYear', $nextYear);

        //Limit to active affiliations
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('affiliation_entity_affiliation.dateEnd'));

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
            default:
                $queryBuilder->addOrderBy('organisation_entity_organisation.id', $direction);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * Give a list of organisations.
     *
     * @param bool $onlyActiveProject
     * @param bool $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisations($onlyActiveProject, $onlyActivePartner)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation');
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.affiliation', 'affiliation_entity_affiliation');
        $queryBuilder->join('affiliation_entity_affiliation.project', 'project_entity_project');

        //Limit to only the active projects
        if ($onlyActiveProject) {
            /** @var Project $projectRepository */
            $projectRepository = $this->getEntityManager()->getRepository(\Project\Entity\Project::class);
            $queryBuilder = $projectRepository->onlyActiveProject($queryBuilder);
        }
        if ($onlyActivePartner) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('affiliation_entity_affiliation.dateEnd'));
        }
        $queryBuilder->orderBy('organisation_entity_organisation.organisation', 'ASC');

        return $queryBuilder->getQuery();
    }

    /**
     * Give a list of organisations by country.
     *
     * @param Country $country
     * @param bool $onlyActiveProject
     * @param bool $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisationByCountry(Country $country, $onlyActiveProject, $onlyActivePartner)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation');
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.affiliation', 'affiliation_entity_affiliation');
        $queryBuilder->join('affiliation_entity_affiliation.project', 'project_entity_project');

        //Limit to only the active projects
        if ($onlyActiveProject) {
            /** @var Project $projectRepository */
            $projectRepository = $this->getEntityManager()->getRepository(\Project\Entity\Project::class);
            $queryBuilder = $projectRepository->onlyActiveProject($queryBuilder);
        }
        if ($onlyActivePartner) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('affiliation_entity_affiliation.dateEnd'));
        }

        $queryBuilder->andWhere('organisation_entity_organisation.country = ?8');
        $queryBuilder->setParameter(8, $country);
        $queryBuilder->orderBy('organisation_entity_organisation.organisation', 'ASC');

        return $queryBuilder->getQuery();
    }

    /**
     * This is basic search for organisations (based on the name, number and description.
     *
     * @param string $searchItem
     * @param int $maxResults
     * @param null $countryId
     * @param bool $onlyActiveProject
     * @param bool $onlyActivePartner
     *
     * @return Entity\Organisation[]
     */
    public function searchOrganisations(
        $searchItem,
        $maxResults = 12,
        $countryId = null,
        $onlyActiveProject = true,
        $onlyActivePartner = true
    ) {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            [
                'organisation_entity_organisation.id',
                'organisation_entity_organisation.organisation',
                'general_entity_country.iso3',
            ]
        );
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');

        $queryBuilder->andWhere('organisation_entity_organisation.organisation LIKE :searchItem');

        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');
        $queryBuilder->leftJoin('organisation_entity_organisation.affiliation', 'affiliation_entity_affiliation');
        $queryBuilder->leftJoin('affiliation_entity_affiliation.project', 'project_entity_project');
        $queryBuilder->setParameter('searchItem', "%" . $searchItem . "%");

        if (!is_null($countryId)) {
            $queryBuilder->andWhere('organisation_entity_organisation.country = ?3');
            $queryBuilder->setParameter(3, $countryId);
        }
        //Limit to only the active projects
        if ($onlyActiveProject) {
            /** @var Project $projectRepository */
            $projectRepository = $this->getEntityManager()->getRepository(\Project\Entity\Project::class);
            $queryBuilder = $projectRepository->onlyActiveProject($queryBuilder);
        }
        if ($onlyActivePartner) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('affiliation_entity_affiliation.dateEnd'));
        }
        $queryBuilder->setMaxResults($maxResults);
        $queryBuilder->orderBy('organisation_entity_organisation.organisation', 'ASC');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param         $name
     * @param Country $country
     * @param         $emailAddress
     *
     * @return Entity\Organisation[]|null
     */
    public function findOrganisationByNameCountryAndEmailAddress($name, Country $country, $emailAddress)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation');
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        //Select projects based on a type

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_web_organisation');
        $subSelect->from(Entity\Web::class, 'organisation_entity_web');
        $subSelect->join('organisation_entity_web.organisation', 'organisation_entity_web_organisation');
        $subSelect->andWhere('organisation_entity_web.web LIKE :domain');

        //Make a second sub-select to cancel out organisations without a domain
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('organisation_entity_web_organisation2');
        $subSelect2->from(Entity\Web::class, 'organisation_entity_web2');
        $subSelect2->join('organisation_entity_web2.organisation', 'organisation_entity_web_organisation2');

        /*
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($emailAddress);
        $queryBuilder->setParameter('domain', "%" . $validateEmail->hostname . "%");
        //We want a match on the email address
        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('organisation_entity_organisation.id', $subSelect->getDQL())
        );

        /**
         * Limit on the country
         */
        $queryBuilder->andWhere('organisation_entity_organisation.country = ?3');
        $queryBuilder->setParameter(3, $country->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Contact $contact
     *
     * @return array
     */
    public function findOrganisationForProfileEditByContact(Contact $contact)
    {
        $organisations = [];
        //Start with your own organisation

        if (!is_null($contact->getContactOrganisation())) {
            $organisations[$contact->getContactOrganisation()->getOrganisation()->getId()]
                = $contact->getContactOrganisation()->getOrganisation();
        }

        foreach ($this->findOrganisationByEmailAddress($contact->getEmail()) as $organisation) {
            $organisations[$organisation->getId()] = $organisation;
        }

        asort($organisations);

        //Add an empty value
        $emptyOrganisation = new Entity\Organisation();
        $emptyOrganisation->setId(0);
        $emptyOrganisation->setOrganisation('&mdash; None of the above');
        $organisations[$emptyOrganisation->getId()] = $emptyOrganisation;

        return $organisations;
    }

    /**
     * @param         $emailAddress
     *
     * @return Entity\Organisation[]|null
     */
    public function findOrganisationByEmailAddress($emailAddress)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation');
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.country', 'general_entity_country');

        //Inner join on contact_organisations to only have active organisations
        $queryBuilder->leftJoin(
            'organisation_entity_organisation.contactOrganisation',
            'organisation_entity_contactorganisation'
        );


        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('organisation_entity_web_organisation');
        $subSelect->from(Entity\Web::class, 'organisation_entity_web');
        $subSelect->join('organisation_entity_web.organisation', 'organisation_entity_web_organisation');
        $subSelect->andWhere('organisation_entity_web.web LIKE :domain');
        $subSelect->andWhere(
            $queryBuilder->expr()->notIn('organisation_entity_web.web', ['gmail.com', 'hotmail.com', 'yahoo.com'])
        );

        /**
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($emailAddress);
        $queryBuilder->setParameter('domain', "%" . $validateEmail->hostname . "%");
        //We want a match on the email address
        $queryBuilder->andWhere($queryBuilder->expr()->in('organisation_entity_organisation.id', $subSelect->getDQL()));

        $queryBuilder->addOrderBy('general_entity_country.country', 'ASC');
        $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', 'ASC');


        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $name
     * @param Country $country
     * @param bool $onlyMain
     *
     * @return Entity\Organisation|null
     */
    public function findOrganisationByNameCountry(string $name, Country $country, bool $onlyMain = true)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation');
        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');

        if (!$onlyMain) {
            $queryBuilder->leftJoin('organisation_entity_organisation.names', 'organisation_entity_name');
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('organisation_entity_name.name', ':searchItem'),
                    $queryBuilder->expr()->like('organisation_entity_organisation.organisation', ':searchItem')
                )
            );
        } else {
            $queryBuilder->andWhere('organisation_entity_organisation.organisation LIKE :searchItem');
        }

        /*
         * Limit on the country
         */
        $queryBuilder->andWhere('organisation_entity_organisation.country = :country');


        /**
         * Do a filter based on the organisation name
         */
        $queryBuilder->setParameter('country', $country);
        $queryBuilder->setParameter('searchItem', "%" . $name . "%");

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Meeting $meeting
     * @param Parameters $search
     *
     * @return Entity\Organisation[]
     */
    public function findOrganisationByMeetingAndDescriptionSearch(Meeting $meeting, Parameters $search)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_organisation', 'partial l.{id}', 'partial ct.{id,extension}');

        $queryBuilder->distinct('organisation_entity_organisation.id');
        $queryBuilder->from(Entity\Organisation::class, 'organisation_entity_organisation');
        $queryBuilder->join('organisation_entity_organisation.contactOrganisation', 'co');
        $queryBuilder->leftJoin('organisation_entity_organisation.description', 'd');
        $queryBuilder->leftJoin('organisation_entity_organisation.logo', 'l');
        $queryBuilder->join('l.contentType', 'ct');

        /*
         * The search can be refined on country and type, include the results here
         */
        if ($search->get('country') && $search->get('country') !== '0') {
            $queryBuilder->join('organisation_entity_organisation.country', 'country');
            $queryBuilder->andWhere('country.id = ?7');
            $queryBuilder->setParameter(7, $search->get('country'));
        }
        /*
         * The search can be refined on country and type, include the results here
         */
        if ($search->get('organisationType')) {
            $queryBuilder->join('organisation_entity_organisation.type', 'type');
            $queryBuilder->andWhere($queryBuilder->expr()->in('type.id', $search->get('organisationType')));
        }
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('d.description', '?4'),
                $queryBuilder->expr()->like('organisation_entity_organisation.organisation', '?4')
            )
        );
        /*
         * Limit the results to the registered users
         */
        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('c');
        $subSelect->from('Event\Entity\Registration', 'r');
        $subSelect->join('r.contact', 'c');
        $subSelect->where('r.meeting = ?1');
        $subSelect->andWhere($subSelect->expr()->isNull('r.dateEnd'));
        $subSelect->andWhere('r.hideInList = ?2');
        $subSelect->andWhere('r.overbooked = ?3');

        $queryBuilder->andWhere($queryBuilder->expr()->in('co.contact', $subSelect->getDQL()));
        $queryBuilder->setParameter(1, $meeting->getId());
        $queryBuilder->setParameter(2, Registration::NOT_HIDE_IN_LIST);
        $queryBuilder->setParameter(3, Registration::NOT_OVERBOOKED);
        $queryBuilder->setParameter(4, '%' . $search->get('search') . '%');
        $queryBuilder->addOrderBy('organisation_entity_organisation.organisation', 'ASC');

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param Entity\Organisation $organisation
     * @return Entity\Organisation[]
     */
    public function findMergeCandidatesFor(Entity\Organisation $organisation)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('o');
        $queryBuilder->from(Entity\Organisation::class, 'o');
        $queryBuilder->join('o.country', 'c');
        $queryBuilder->leftJoin('o.financial', 'f');
        $queryBuilder->where($queryBuilder->expr()->neq('o.id', ':organisationId'));

        // Exclude when both organisations have a different VAT number
        if (!is_null($organisation->getFinancial())) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->isNull('f.vat'),
                $queryBuilder->expr()->neq('f.vat', ':vat')
            ));
            $queryBuilder->setParameter('vat', $organisation->getFinancial()->getVat());
        }

        // Exclude when both organisations are parent organisations
        if (!is_null($organisation->getParent())) {
            $queryBuilder->leftJoin('o.parent', 'organisation_entity_parent');
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('organisation_entity_parent.id'));
        }

        $queryBuilder->andWhere($queryBuilder->expr()->like('o.organisation', ':organisationName'));
        $queryBuilder->andWhere($queryBuilder->expr()->eq('o.country', ':country'));
        $queryBuilder->orderBy('o.organisation', Criteria::ASC);
        $queryBuilder->addOrderBy('c.country', Criteria::ASC);

        $queryBuilder->setParameter('organisationId', $organisation->getId());
        $queryBuilder->setParameter('organisationName', '%' . $organisation->getOrganisation() . '%');
        $queryBuilder->setParameter('country', $organisation->getCountry());

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult();
    }
}
