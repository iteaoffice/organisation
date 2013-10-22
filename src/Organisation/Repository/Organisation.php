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

/**
 * @category    Organisation
 * @package     Repository
 */
class Organisation extends EntityRepository
{
    /**
     * Give a list of organisations
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
}