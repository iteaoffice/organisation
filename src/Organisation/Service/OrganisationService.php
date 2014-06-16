<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\Service;

use Event\Entity\Meeting\Meeting;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Project\Entity\Project;
use Zend\Stdlib\Parameters;

/**
 * OrganisationService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class OrganisationService extends ServiceAbstract
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var Organisation
     */
    protected $organisation;

    /**
     * @param int $id
     *
     * @return OrganisationService;
     */
    public function setOrganisationId($id)
    {
        $this->setOrganisation($this->findEntityById('organisation', $id));

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->organisation) || is_null($this->organisation->getId());
    }

    /**
     * @param $docRef
     *
     * @return null|OrganisationService
     */
    public function findOrganisationByDocRef($docRef)
    {
        $organisation = $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))->findOneBy(
            array('docRef' => $docRef)
        );

        /**
         * Return null when no project can be found
         */
        if (is_null($organisation)) {
            return null;
        }

        $this->setOrganisation($organisation);

        return $this->createServiceElement($organisation);
    }

    /**
     * @param Organisation $organisation
     *
     * @return OrganisationService
     */
    private function createServiceElement(Organisation $organisation)
    {
        $organisationService               = clone $this;
        $organisationService->organisation = $organisation;

        return $organisationService;
    }

    /**
     * @param $branch
     *
     * @return string
     */
    public function parseOrganisationWithBranch($branch)
    {
        return trim(
            preg_replace('/^(([^\~]*)\~\s?)?\s?(.*)$/', '${2}' . $this->getOrganisation() . ' ${3}', $branch)
        );
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param \Organisation\Entity\Organisation $organisation
     *
     * @return OrganisationService
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Give a list of organisations. A flag can be triggered to toggle only active projects
     *
     * @param bool $onlyActive
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisations($onlyActive = true)
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('organisation')
        )->findOrganisations($onlyActive);
    }

    /**
     * Give a list of organisations per country. A flag can be triggered to toggle only active projects
     *
     * @param Country $country
     * @param bool    $onlyActive
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisationByCountry(Country $country, $onlyActive = true)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))
                    ->findOrganisationByCountry($country, $onlyActive);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject and the email address
     *
     * @param string  $name
     * @param Country $country
     * @param string  $emailAddress
     *
     * @return Organisation[]
     */
    public function findOrganisationByNameCountryAndEmailAddress($name, Country $country, $emailAddress)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('Organisation'))
                    ->findOrganisationByNameCountryAndEmailAddress($name, $country, $emailAddress);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject
     *
     * @param string  $name
     * @param Country $country
     *
     * @return Organisation
     */
    public function findOrganisationByNameCountry($name, Country $country)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('Organisation'))
                    ->findOrganisationByNameCountry($name, $country);
    }

    /**
     * @param Meeting    $meeting
     * @param Parameters $search
     *
     * @return Organisation[]
     */
    public function findOrganisationByMeetingAndDescriptionSearch(Meeting $meeting, Parameters $search)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('Organisation'))
                    ->findOrganisationByMeetingAndDescriptionSearch($meeting, $search);
    }

    /**
     * Produce a list of organisations for a project (only active)
     *
     * @param Project $project
     * @param bool    $onlyActive
     *
     * @return OrganisationService[]
     */
    public function findOrganisationByProject(Project $project, $onlyActive = true)
    {
        $organisations = [];

        foreach ($project->getAffiliation() as $affiliation) {
            if ($onlyActive && is_null($affiliation->getDateEnd())) {
                //Add the organisation in the key to sort on it
                $organisations[$affiliation->getOrganisation()->getOrganisation()] = $this->createServiceElement(
                    $affiliation->getOrganisation()
                );
            }
        }

        //Sort on the key (ASC)
        ksort($organisations);

        return $organisations;
    }

    /**
     * Search for organisations based on a search-item
     *
     * @param      $searchItem
     * @param      $maxResults
     * @param null $countryId
     * @param bool $onlyActive
     *
     * @return Organisation[]
     */
    public function searchOrganisation($searchItem, $maxResults, $countryId = null, $onlyActive = true)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))
                    ->searchOrganisations($searchItem, $maxResults, $countryId, $onlyActive);
    }
}
