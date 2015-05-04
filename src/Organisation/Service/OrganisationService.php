<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\Service;

use Event\Entity\Meeting\Meeting;
use General\Entity\Country;
use Organisation\Entity\Organisation;
use Organisation\Entity\Type;
use Program\Entity\Program;
use Project\Entity\Project;
use Zend\Stdlib\Parameters;

/**
 * OrganisationService.
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 */
class OrganisationService extends ServiceAbstract
{
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
        /*
         * @var $organisation Organisation
         */
        $organisation = $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))->findOneBy(
            ['docRef' => $docRef]
        );
        /*
         * Return null when no project can be found
         */
        if (is_null($organisation)) {
            return;
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
        $organisationService = clone $this;
        $organisationService->organisation = $organisation;

        return $organisationService;
    }

    /**
     * @param $branch
     * @param Organisation $organisation = null
     *
     * @return string
     */
    public function parseOrganisationWithBranch($branch, Organisation $organisation = null)
    {
        if (is_null($organisation)) {
            $organisation = $this->getOrganisation();
        }

        return trim(
            preg_replace('/^(([^\~]*)\~\s?)?\s?(.*)$/', '${2}' . $organisation . ' ${3}', $branch)
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
     * @return Type[]
     */
    public function findOrganisationTypes()
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('type'))->findBy(
            [],
            ['type' => 'ASC']
        );
    }

    /**
     * Give a list of organisations. A flag can be triggered to toggle only active projects.
     *
     * @param bool $onlyActiveProject
     * @param bool $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisations($onlyActiveProject = true, $onlyActivePartner = true)
    {
        return $this->getEntityManager()->getRepository(
            $this->getFullEntityName('organisation')
        )->findOrganisations($onlyActiveProject, $onlyActivePartner);
    }

    /**
     * Give a list of organisations per country. A flag can be triggered to toggle only active projects.
     *
     * @param Country $country
     * @param bool $onlyActiveProject
     * @param bool $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisationByCountry(Country $country, $onlyActiveProject = true, $onlyActivePartner = true)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))
            ->findOrganisationByCountry($country, $onlyActiveProject, $onlyActivePartner);
    }

    /**
     * @param Organisation $organisation
     * @return array
     */
    public function findBranchesByOrganisation(Organisation $organisation)
    {
        $branches = [];

        $this->setOrganisation($organisation);

        foreach ($organisation->getContactOrganisation() as $contactOrganisation) {
            $branches[$contactOrganisation->getBranch()] = $this->parseOrganisationWithBranch($contactOrganisation->getBranch());
        }

        return array_unique($branches);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject and the email address.
     *
     * @param string $name
     * @param Country $country
     * @param string $emailAddress
     *
     * @return Organisation[]
     */
    public function findOrganisationByNameCountryAndEmailAddress($name, Country $country, $emailAddress)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('Organisation'))
            ->findOrganisationByNameCountryAndEmailAddress($name, $country, $emailAddress);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject.
     *
     * @param string $name
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
     * @param Meeting $meeting
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
     * Produce a list of organisations for a project (only active).
     *
     * @param Project $project
     * @param bool $onlyActiveProject
     *
     * @return OrganisationService[]
     */
    public function findOrganisationByProject(Project $project, $onlyActiveProject = true)
    {
        $organisations = [];
        foreach ($project->getAffiliation() as $affiliation) {
            if ($onlyActiveProject && is_null($affiliation->getDateEnd())) {
                //Add the organisation in the key to sort on it
                $organisations[sprintf("%s-%s", $affiliation->getOrganisation()->getOrganisation(),
                    $affiliation->getOrganisation()->getCountry()->getCountry())] = $this->createServiceElement(
                    $affiliation->getOrganisation()
                );
            }
        }
        //Sort on the key (ASC)
        ksort($organisations);

        return $organisations;
    }

    /**
     * Checks if the affiliation has a DOA.
     *
     * @param Program $program
     *
     * @return bool
     */
    public function hasDoaForProgram(Program $program)
    {

        foreach ($this->organisation->getProgramDoa() as $doa) {
            if ($doa->getProgram()->getId() === $program->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search for organisations based on a search-item.
     *
     * @param      $searchItem
     * @param      $maxResults
     * @param null $countryId
     * @param bool $onlyActiveProject
     *
     * @return Organisation[]
     */
    public function searchOrganisation($searchItem, $maxResults, $countryId = null, $onlyActiveProject = true)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))
            ->searchOrganisations($searchItem, $maxResults, $countryId, $onlyActiveProject);
    }
}
