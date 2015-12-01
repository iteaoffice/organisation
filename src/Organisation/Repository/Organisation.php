<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Repository;

use Contact\Entity\Contact;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Event\Entity\Meeting\Meeting;
use Event\Entity\Registration;
use General\Entity\Country;
use Organisation\Entity;
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
        $queryBuilder->select('o');
        $queryBuilder->from('Organisation\Entity\Organisation', 'o');


        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('o.organisation', ':like'));
            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }

        if (array_key_exists('type', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('o.type', implode($filter['type'], ', ')));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'])) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'lastUpdate':
                $queryBuilder->addOrderBy('o.lastUpdate', $direction);
                break;
            case 'name':
                $queryBuilder->addOrderBy('o.organisation', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('o.id', $direction);

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
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');
        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->join('o.affiliation', 'a');
        $qb->join('a.project', 'p');
        //Limit to only the active projects
        if ($onlyActiveProject) {
            $qb = $this->getEntityManager()->getRepository('Project\Entity\Project')->onlyActiveProject($qb);
        }
        if ($onlyActivePartner) {
            $qb->andWhere($qb->expr()->isNull('a.dateEnd'));
        }
        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery();
    }

    /**
     * Give a list of organisations by country.
     *
     * @param Country $country
     * @param bool    $onlyActiveProject
     * @param bool    $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisationByCountry(Country $country, $onlyActiveProject, $onlyActivePartner)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');
        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->join('o.affiliation', 'a');
        $qb->join('a.project', 'p');
        //Limit to only the active projects
        if ($onlyActiveProject) {
            $qb = $this->getEntityManager()->getRepository('Project\Entity\Project')->onlyActiveProject($qb);
        }
        if ($onlyActivePartner) {
            $qb->andWhere($qb->expr()->isNull('a.dateEnd'));
        }
        $qb->andWhere('o.country = ?8');
        $qb->setParameter(8, $country);
        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery();
    }

    /**
     * This is basic search for organisations (based on the name, number and description.
     *
     * @param string $searchItem
     * @param int    $maxResults
     * @param null   $countryId
     * @param bool   $onlyActiveProject
     * @param bool   $onlyActivePartner
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
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['o.id', 'o.organisation', 'c.iso3']);
        $qb->distinct('o.id');
        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->join('o.country', 'c');
        $qb->join('o.affiliation', 'a');
        $qb->join('a.project', 'p');
        $qb->setParameter('searchItem', "%" . $searchItem . "%");
        if (!is_null($countryId)) {
            $qb->andWhere('o.country = ?3');
            $qb->setParameter(3, $countryId);
        }
        //Limit to only the active projects
        if ($onlyActiveProject) {
            $qb = $this->getEntityManager()->getRepository('Project\Entity\Project')->onlyActiveProject($qb);
        }
        if ($onlyActivePartner) {
            $qb->andWhere($qb->expr()->isNotNull('a.dateEnd'));
        }
        $qb->setMaxResults($maxResults);
        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery()->getArrayResult();
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
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');
        $qb->from('Organisation\Entity\Organisation', 'o');
        //Select projects based on a type

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('wo');
        $subSelect->from('Organisation\Entity\Web', 'w');
        $subSelect->join('w.organisation', 'wo');
        $subSelect->andWhere('w.web LIKE :domain');

        //Make a second sub-select to cancel out organisations without a domain
        $subSelect2 = $this->_em->createQueryBuilder();
        $subSelect2->select('wo2');
        $subSelect2->from('Organisation\Entity\Web', 'web2');
        $subSelect2->join('web2.organisation', 'wo2');

        /*
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($emailAddress);
        $qb->setParameter('domain', "%" . $validateEmail->hostname . "%");
        //We want a match on the email address
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->in(
                'o.id',
                $subSelect->getDQL()
            ),
            $qb->expr()->notIn('o.id', $subSelect2->getDQL())
        ));

        /*
         * Limit on the country
         */
        $qb->andWhere('o.country = ?3');
        $qb->setParameter(3, $country->getId());
        /*
         * Do a filter based on the organisation name
         */
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $name . "%");

        return $qb->getQuery()->getResult();
    }

    /**
     * @param         $emailAddress
     *
     * @return Entity\Organisation[]|null
     */
    public function findOrganisationByEmailAddress($emailAddress)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');
        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->join('o.country', 'c');

        //Inner join on contact_organisations to only have active organisations
        $qb->join('o.contactOrganisation', 'co');


        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('wo');
        $subSelect->from('Organisation\Entity\Web', 'w');
        $subSelect->join('w.organisation', 'wo');
        $subSelect->andWhere('w.web LIKE :domain');
        $subSelect->andWhere($qb->expr()->notIn('w.web', ['gmail.com', 'hotmail.com', 'yahoo.com']));

        /**
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($emailAddress);
        $qb->setParameter('domain', "%" . $validateEmail->hostname . "%");
        //We want a match on the email address
        $qb->andWhere($qb->expr()->in('o.id', $subSelect->getDQL()));

        $qb->addOrderBy('c.country', 'ASC');
        $qb->addOrderBy('o.organisation', 'ASC');


        return $qb->getQuery()->getResult();
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
     * @param         $name
     * @param Country $country
     *
     * @return Entity\Organisation|null
     */
    public function findOrganisationByNameCountry($name, Country $country)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');
        $qb->from('Organisation\Entity\Organisation', 'o');
        /*
         * Limit on the country
         */
        $qb->andWhere('o.country = ?3');
        $qb->setParameter(3, $country->getId());
        /*
         * Do a filter based on the organisation name
         */
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $name . "%");

        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Find participants based on the given criteria.
     *
     * @param Meeting $meeting
     * @param Parameters $search
     *
     * @return Registration[]
     */
    public function findOrganisationByMeetingAndDescriptionSearch(Meeting $meeting, Parameters $search)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('o', 'partial l.{id}', 'partial ct.{id,extension}');

        $queryBuilder->distinct('o.id');
        $queryBuilder->from('Organisation\Entity\Organisation', 'o');
        $queryBuilder->join('o.contactOrganisation', 'co');
        $queryBuilder->leftJoin('o.description', 'd');
        $queryBuilder->leftJoin('o.logo', 'l');
        $queryBuilder->join('l.contentType', 'ct');

        /*
         * The search can be refined on country and type, include the results here
         */
        if ($search->get('country') && $search->get('country') !== '0') {
            $queryBuilder->join('o.country', 'country');
            $queryBuilder->andWhere('country.id = ?7');
            $queryBuilder->setParameter(7, $search->get('country'));
        }
        /*
         * The search can be refined on country and type, include the results here
         */
        if ($search->get('organisationType')) {
            $queryBuilder->join('o.type', 'type');
            $queryBuilder->andWhere($queryBuilder->expr()->in('type.id', $search->get('organisationType')));
        }
        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            $queryBuilder->expr()->like(
                'd.description',
                '?4'
            ),
            $queryBuilder->expr()->like('o.organisation', '?4')
        ));
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
        $queryBuilder->addOrderBy('o.organisation', 'ASC');

        return $queryBuilder->getQuery()->useQueryCache(true)->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}
