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
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return \Organisation\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
