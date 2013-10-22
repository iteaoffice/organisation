<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     Service
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Organisation\Service;

use Organisation\Entity\Organisation;
use General\Entity\Country;
use Project\Entity\Project;

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
     * @param $docRef
     *
     * @return null|OrganisationService
     */
    public function findOrganisationByDocRef($docRef)
    {
        $organisation = $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))->findOneBy(
            array('docRef' => $docRef));

        /**
         * Return null when no project can be found
         */
        if (is_null($organisation)) {
            return null;
        }

        return $this->createServiceElement($organisation);
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
     * Give a list of organisations per country. A flag can be triggered to toggle only active projects
     *
     * @param Country $country
     * @param bool    $onlyActive
     *
     * @return array
     */
    public function findOrganisationByCountry(Country $country, $onlyActive = true)
    {
        $organisations = array();

        $organisationPerCountry = $this->getEntityManager()->getRepository($this->getFullEntityName('organisation'))
            ->findOrganisationByCountry($country, $onlyActive);

        foreach ($organisationPerCountry as $organisation) {
            $organisations[] = $this->createServiceElement($organisation);
        }

        return $organisations;
    }

    /**
     * Produce a list of organisations for a project (only active)
     *
     * @param Project $project
     *
     * @return OrganisationService[]
     */
    public function findOrganisationByProject(Project $project)
    {
        $organisations = array();

        foreach ($project->getAffiliation() as $affiliation) {
            if (is_null($affiliation->getDateEnd())) {
                $organisations[] = $this->createServiceElement($affiliation->getOrganisation());
            }
        }

        return $organisations;
    }


    /**
     * @param Organisation $organisation
     *
     * @return OrganisationService
     */
    private function createServiceElement(Organisation $organisation)
    {
        $organisationService = new self();
        $organisationService->setServiceLocator($this->getServiceLocator());
        $organisationService->organisation = $organisation;

        return $organisationService;
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
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
