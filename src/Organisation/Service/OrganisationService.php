<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Service;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Service\ContactService;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Event\Entity\Meeting\Meeting;
use General\Entity\Country;
use Organisation\Entity\Financial;
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
        return is_null($this->organisation)
        || is_null($this->organisation->getId());
    }

    /**
     * @return string
     */
    public function parseDebtorNumber()
    {
        return trim(sprintf("%'.06d\n", 100000 + $this->getOrganisation()->getId()));
    }

    /**
     * @return string
     */
    public function parseCreditNumber()
    {
        return trim(sprintf("%'.06d\n", 200000 + $this->getOrganisation()->getId()));
    }


    /**
     * @param $filter
     *
     * @return QueryBuilder
     */
    public function findActiveOrganisationWithoutFinancial($filter)
    {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findActiveOrganisationWithoutFinancial($filter);
    }

    /**
     * @param  Contact $contact
     *
     * @return Organisation[];
     */
    public function findOrganisationForProfileEditByContact(Contact $contact)
    {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findOrganisationForProfileEditByContact($contact);
    }

    /**
     * @param  int $which
     *
     * @return int
     */
    public function getAffiliationCount($which = AffiliationService::WHICH_ALL)
    {
        return ($this->getOrganisation()->getAffiliation()->filter(function (
            Affiliation $affiliation
        ) use ($which) {
            switch ($which) {
                case AffiliationService::WHICH_ONLY_ACTIVE:
                    return is_null($affiliation->getDateEnd());
                case AffiliationService::WHICH_ONLY_INACTIVE:
                    return !is_null($affiliation->getDateEnd());
                default:
                    return true;
            }

        })->count());
    }

    /**
     * @param  int $which
     *
     * @return int
     */
    public function getContactCount($which = ContactService::WHICH_ONLY_ACTIVE)
    {
        return ($this->getOrganisation()->getContactOrganisation()->filter(function (
            ContactOrganisation $contactOrganisation
        ) use (
            $which
        ) {
            switch ($which) {
                case ContactService::WHICH_ONLY_ACTIVE:
                    return is_null($contactOrganisation->getContact()->getDateEnd());
                case ContactService::WHICH_ONLY_EXPIRED:
                    return !is_null($contactOrganisation->getContact()->getDateEnd());
                default:
                    return true;
            }

        })->count());
    }

    /**
     * @return Query
     */
    public function findOrganisationFinancialList($filter)
    {
        return $this->getEntityManager()->getRepository(Financial::class)->findOrganisationFinancialList($filter);
    }

    /**
     * @param Organisation $organisation
     *
     * @return Contact|\Contact\Entity\Selection|null
     */
    public function findFinancialContact(Organisation $organisation)
    {
        /**
         * The financial contact can be found be taking the contact which has the most invoices on his/her name
         */
        $invoiceContactList = [];
        foreach ($organisation->getInvoice() as $invoice) {
            $invoiceContactList[] = $invoice->getContact()->getId();
        }

        if (sizeof($invoiceContactList) === 0) {
            return null;
        }

        $values = array_count_values($invoiceContactList);
        arsort($values);

        $contactId = array_keys($values)[0];

        return $this->getEntityManager()->find(Contact::class, $contactId);
    }

    /**
     * @param $docRef
     *
     * @return null|OrganisationService
     */
    public function findOrganisationByDocRef($docRef)
    {
        /**
         * @var $organisation Organisation
         */
        $organisation = $this->getEntityManager()->getRepository(Organisation::class)->findOneBy(['docRef' => $docRef]);
        /*
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
        $organisationService = clone $this;
        $organisationService->organisation = $organisation;

        return $organisationService;
    }

    /**
     * @param              $branch
     * @param Organisation $organisation = null
     *
     * @return string
     */
    public function parseOrganisationWithBranch(
        $branch,
        Organisation $organisation = null
    ) {
        if (is_null($organisation)) {
            $organisation = $this->getOrganisation();
        }

        return trim(preg_replace('/^(([^\~]*)\~\s?)?\s?(.*)$/', '${2}' . $organisation . ' ${3}', $branch));
    }

    /**
     * @return Type[]
     */
    public function findOrganisationTypes()
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('type'))
            ->findBy([], ['type' => 'ASC']);
    }

    /**
     * Give a list of organisations. A flag can be triggered to toggle only active projects.
     *
     * @param bool $onlyActiveProject
     * @param bool $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisations(
        $onlyActiveProject = true,
        $onlyActivePartner = true
    ) {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findOrganisations($onlyActiveProject, $onlyActivePartner);
    }

    /**
     * Give a list of organisations per country. A flag can be triggered to toggle only active projects.
     *
     * @param Country $country
     * @param bool    $onlyActiveProject
     * @param bool    $onlyActivePartner
     *
     * @return \Doctrine\ORM\Query
     */
    public function findOrganisationByCountry(
        Country $country,
        $onlyActiveProject = true,
        $onlyActivePartner = true
    ) {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findOrganisationByCountry($country, $onlyActiveProject, $onlyActivePartner);
    }

    /**
     * @param  Organisation $organisation
     *
     * @return array
     */
    public function findBranchesByOrganisation(Organisation $organisation)
    {
        $branches = [];

        $this->setOrganisation($organisation);

        foreach ($organisation->getContactOrganisation() as $contactOrganisation) {
            $branches[$contactOrganisation->getBranch()]
                = $this->parseOrganisationWithBranch($contactOrganisation->getBranch());
        }

        return array_unique($branches);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject and the email address.
     *
     * @param string  $name
     * @param Country $country
     * @param string  $emailAddress
     *
     * @return Organisation[]
     */
    public function findOrganisationByNameCountryAndEmailAddress(
        $name,
        Country $country,
        $emailAddress
    ) {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findOrganisationByNameCountryAndEmailAddress($name, $country, $emailAddress);
    }

    /**
     * @param $vat
     *
     * @return Financial|null
     */
    public function findFinancialOrganisationWithVAT($vat)
    {
        return $this->getEntityManager()->getRepository(Financial::class)
            ->findOneBy(['vat' => $vat]);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject.
     *
     * @param string  $name
     * @param Country $country
     *
     * @return Organisation
     */
    public function findOrganisationByNameCountry($name, Country $country)
    {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findOrganisationByNameCountry($name, $country);
    }

    /**
     * @param Meeting    $meeting
     * @param Parameters $search
     *
     * @return Organisation[]
     */
    public function findOrganisationByMeetingAndDescriptionSearch(
        Meeting $meeting,
        Parameters $search
    ) {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->findOrganisationByMeetingAndDescriptionSearch($meeting, $search);
    }

    /**
     * Produce a list of organisations for a project (only active).
     *
     * @param Project $project
     * @param bool    $onlyActiveProject
     *
     * @return OrganisationService[]
     */
    public function findOrganisationByProject(
        Project $project,
        $onlyActiveProject = true
    ) {
        $organisations = [];
        foreach ($project->getAffiliation() as $affiliation) {
            if ($onlyActiveProject && is_null($affiliation->getDateEnd())) {
                //Add the organisation in the key to sort on it
                $organisations[sprintf(
                    "%s-%s",
                    $affiliation->getOrganisation()->getOrganisation(),
                    $affiliation->getOrganisation()->getCountry()->getCountry()
                )]
                    = $this->createServiceElement($affiliation->getOrganisation());
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
     * @param bool $onlyActivePartner
     *
     * @return Organisation[]
     */
    public function searchOrganisation(
        $searchItem,
        $maxResults,
        $countryId = null,
        $onlyActiveProject = true,
        $onlyActivePartner = true
    ) {
        return $this->getEntityManager()->getRepository(Organisation::class)
            ->searchOrganisations($searchItem, $maxResults, $countryId, $onlyActiveProject, $onlyActivePartner);
    }
}
