<?php
/**
 * DebraNova copyright message placeholder
 *
 * @category    Organisation
 * @package     Repository
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Organisation\Repository;

use Doctrine\ORM\EntityRepository;

use General\Entity\Country;
use Organisation\Entity;
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
     * @param         $onlyActive
     *
     * @return Entity\Organisation[];
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
            $projectRepository = $this->getEntityManager()->getRepository('Project\Entity\Project');
            $qb                = $projectRepository->onlyActiveProject($qb);
        }

        $qb->orderBy('o.organisation', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Give a list of organisations by country
     *
     * @param Country $country
     * @param         $onlyActive
     *
     * @return Entity\Organisation[];
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
            $projectRepository = $this->getEntityManager()->getRepository('Project\Entity\Project');
            $qb                = $projectRepository->onlyActiveProject($qb);
        }

        $qb->andWhere('o.country = ?8');
        $qb->setParameter(8, $country);


        return $qb->getQuery()->getResult();
    }

    /**
     * @param         $name
     * @param Country $country
     * @param         $emailAddress
     *
     * @return Entity\Organisation|null
     */
    public function findOrganisationByNameCountryAndEmailAddress($name, Country $country, $emailAddress)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o');
        $qb->distinct('o.id');

        $qb->from('Organisation\Entity\Organisation', 'o');

        /**
         * Do a filter based on the organisation name
         */
        $qb->andWhere('o.organisation LIKE :searchItem');
        $qb->setParameter('searchItem', "%" . $name . "%");

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

        $qb->orWhere($qb->expr()->in('o.id', $subSelect->getDQL()));

        /**
         * Limit on the country
         */
        $qb->andWhere('o.country = ?3');
        $qb->setParameter(3, $country->getId());

        return $qb->getQuery()->getResult();
    }
}