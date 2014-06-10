<?php
/**
 * DebraNova copyright message placeholder
 *
 * @category    Organisation
 * @package     Repository
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\Repository;

use Doctrine\ORM\EntityRepository;
use Event\Entity\Meeting\Meeting;
use Event\Entity\Registration;
use General\Entity\Country;
use Organisation\Entity;
use Zend\Stdlib\Parameters;
use Zend\Validator\EmailAddress;

/**
 * @category    Organisation
 * @package     Repository
 */
class Organisation extends EntityRepository
{
    /**
     * Give a list of organisations
     *
     * @param   $onlyActive
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisations($onlyActive)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');

        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->join('o.affiliation', 'a');
        $qb->join('a.project', 'p');

        //Limit to only the active projects
        if ($onlyActive) {
            $qb = $this->getEntityManager()->getRepository('Project\Entity\Project')->onlyActiveProject($qb);
        }

        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery();
    }

    /**
     * Give a list of organisations by country
     *
     * @param Country $country
     * @param         $onlyActive
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisationByCountry(Country $country, $onlyActive)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');

        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->join('o.affiliation', 'a');
        $qb->join('a.project', 'p');

        //Limit to only the active projects
        if ($onlyActive) {
            $qb = $this->getEntityManager()->getRepository('Project\Entity\Project')->onlyActiveProject($qb);
        }

        $qb->andWhere('o.country = ?8');
        $qb->setParameter(8, $country);

        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery();
    }

    /**
     * This is basic search for organisations (based on the name, number and description
     *
     * @param string $searchItem
     * @param int    $maxResults
     * @param null   $countryId
     * @param bool   $onlyActivegit com
     *
     * @return Entity\Organisation[]
     */
    public function searchOrganisations($searchItem, $maxResults = 12, $countryId = null, $onlyActive = true)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');

        $qb->from('Organisation\Entity\Organisation', 'o');
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $searchItem . "%");

        if (!is_null($countryId)) {
            $qb->andWhere('o.country = ?3');
            $qb->setParameter(3, $countryId);
        }

        //Limit to only the active projects
        if ($onlyActive) {
            $qb->join('o.affiliation', 'a');
            $qb->join('a.project', 'p');
            $qb = $this->getEntityManager()->getRepository('Project\Entity\Project')->onlyActiveProject($qb);
        }

        $qb->setMaxResults($maxResults);

        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery()->getResult();
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

        /**
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($emailAddress);

        $qb->setParameter('domain', "%" . $validateEmail->hostname . "%");

        //We want a match on the email address
        $qb->andWhere($qb->expr()->in('o.id', $subSelect->getDQL()));

        /**
         * Limit on the country
         */
        $qb->andWhere('o.country = ?3');
        $qb->setParameter(3, $country->getId());

        /**
         * Do a filter based on the organisation name
         */
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $name . "%");

        return $qb->getQuery()->getResult();
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

        /**
         * Limit on the country
         */
        $qb->andWhere('o.country = ?3');
        $qb->setParameter(3, $country->getId());

        /**
         * Do a filter based on the organisation name
         */
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $name . "%");

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Find participants based on the given criteria
     *
     * @param Meeting    $meeting
     * @param Parameters $search
     *
     * @return Registration[]
     */
    public function findOrganisationByMeetingAndDescriptionSearch(Meeting $meeting, Parameters $search)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('o');
        $queryBuilder->distinct('o.id');

        $queryBuilder->from('Organisation\Entity\Organisation', 'o');
        $queryBuilder->join('o.contactOrganisation', 'co');
        $queryBuilder->join('o.description', 'd');

        /**
         * The search can be refined on country and type, include the results here
         */
        if ($search->get('country') && $search->get('country') !== '0') {
            $queryBuilder->join('o.country', 'country');
            $queryBuilder->andWhere('country.id = ?7');
            $queryBuilder->setParameter(7, $search->get('country'));
        }

        /**
         * The search can be refined on country and type, include the results here
         */
        if ($search->get('organisationType')) {
            $queryBuilder->join('o.type', 'type');
            $queryBuilder->andWhere($queryBuilder->expr()->in('type.id', $search->get('organisationType')));
        }

        $queryBuilder->andWhere($queryBuilder->expr()->like('d.description', '?4'));

        /**
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

        return $queryBuilder->getQuery()->getResult();
    }
}
