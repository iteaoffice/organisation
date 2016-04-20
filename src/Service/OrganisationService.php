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
use Organisation\Repository;
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
     * @param $id
     *
     * @return null|Organisation
     */
    public function findOrganisationById($id)
    {
        return $this->getEntityManager()->getRepository(Organisation::class)->find($id);
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseDebtorNumber(Organisation $organisation)
    {
        return trim(sprintf("%'.06d\n", 100000 + $organisation->getId()));
    }

    /**
     * @param Organisation $organisation
     *
     * @return string
     */
    public function parseCreditNumber(Organisation $organisation)
    {
        return trim(sprintf("%'.06d\n", 200000 + $organisation->getId()));
    }


    /**
     * @param $filter
     *
     * @return QueryBuilder
     */
    public function findActiveOrganisationWithoutFinancial($filter)
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findActiveOrganisationWithoutFinancial($filter);
    }

    /**
     * @param  Contact $contact
     *
     * @return Organisation[];
     */
    public function findOrganisationForProfileEditByContact(Contact $contact)
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findOrganisationForProfileEditByContact($contact);
    }

    /**
     * @param Organisation $organisation
     * @param int          $which
     *
     * @return int
     */
    public function getAffiliationCount(Organisation $organisation, $which = AffiliationService::WHICH_ALL)
    {
        return ($organisation->getAffiliation()->filter(function (
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
     * @param Organisation $organisation
     * @param int          $which
     *
     * @return int
     */
    public function getContactCount(Organisation $organisation, $which = ContactService::WHICH_ONLY_ACTIVE)
    {
        return ($organisation->getContactOrganisation()->filter(function (
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
     * @param $filter
     *
     * @return Query
     */
    public function findOrganisationFinancialList($filter)
    {
        /** @var Repository\Financial $repository */
        $repository = $this->getEntityManager()->getRepository(Financial::class);

        return $repository->findOrganisationFinancialList($filter);
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
     * @return null|Organisation
     */
    public function findOrganisationByDocRef($docRef)
    {
        return $this->getEntityManager()->getRepository(Organisation::class)->findOneBy(['docRef' => $docRef]);
    }


    /**
     * @param              $branch
     * @param Organisation $organisation = null
     *
     * @return string
     */
    public function parseOrganisationWithBranch(
        $branch,
        Organisation $organisation
    ) {
        return trim(preg_replace('/^(([^\~]*)\~\s?)?\s?(.*)$/', '${2}' . $organisation . ' ${3}', $branch));
    }

    /**
     * @return Type[]
     */
    public function findOrganisationTypes()
    {
        return $this->getEntityManager()->getRepository(Type::class)->findBy([], ['type' => 'ASC']);
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
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findOrganisations($onlyActiveProject, $onlyActivePartner);
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
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findOrganisationByCountry($country, $onlyActiveProject, $onlyActivePartner);
    }

    /**
     * @param  Organisation $organisation
     *
     * @return array
     */
    public function findBranchesByOrganisation(Organisation $organisation)
    {
        $branches = [];

        foreach ($organisation->getContactOrganisation() as $contactOrganisation) {
            $branches[$contactOrganisation->getBranch()]
                = $this->parseOrganisationWithBranch($contactOrganisation->getBranch(), $organisation);
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
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findOrganisationByNameCountryAndEmailAddress($name, $country, $emailAddress);
    }

    /**
     * @param $vat
     *
     * @return Financial|null
     */
    public function findFinancialOrganisationWithVAT($vat)
    {
        return $this->getEntityManager()->getRepository(Financial::class)->findOneBy(['vat' => $vat]);
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
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findOrganisationByNameCountry($name, $country);
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
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->findOrganisationByMeetingAndDescriptionSearch($meeting, $search);
    }

    /**
     * Produce a list of organisations for a project (only active).
     *
     * @param Project $project
     * @param bool    $onlyActiveProject
     *
     * @return Organisation[]
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
                    = $affiliation->getOrganisation();
            }
        }
        //Sort on the key (ASC)
        ksort($organisations);

        return $organisations;
    }

    /**
     * Checks if the affiliation has a DOA.
     *
     * @param Organisation $organisation
     * @param Program      $program
     *
     * @return bool
     */
    public function hasDoaForProgram(Organisation $organisation, Program $program)
    {
        foreach ($organisation->getProgramDoa() as $doa) {
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
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Organisation::class);

        return $repository->searchOrganisations(
            $searchItem,
            $maxResults,
            $countryId,
            $onlyActiveProject,
            $onlyActivePartner
        );
    }
}
