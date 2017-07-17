<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Service;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Contact\Entity\Contact;
use Contact\Entity\ContactOrganisation;
use Contact\Service\ContactService;
use Doctrine\ORM\Query;
use Event\Entity\Meeting\Meeting;
use General\Entity\Country;
use Organisation\Entity;
use Organisation\Repository;
use Program\Entity\Program;
use Project\Entity\Project;
use Project\Entity\Result\Result;
use Zend\Stdlib\Parameters;
use Zend\Validator\EmailAddress;

/**
 * Class OrganisationService
 *
 * @package Organisation\Service
 */
class OrganisationService extends AbstractService
{
    /**
     * @param string $givenName
     * @param string $organisation
     *
     * @return string
     */
    public static function determineBranch(string $givenName, string $organisation): string
    {
        //when the names are identical
        if ($givenName === $organisation) {
            return '';
        }

        /** When the name is not found in the organisation */
        if (strpos($givenName, $organisation) === false) {
            return sprintf("!%s", $givenName);
        }

        return str_replace($organisation, '~', $givenName);
    }

    /**
     * Function which checks if an organisation can be deleted
     *
     * @param Entity\Organisation $organisation
     *
     * @return bool
     */
    public function canDeleteOrganisation(Entity\Organisation $organisation): bool
    {
        return
            $organisation->getContactOrganisation()->isEmpty()
            && $organisation->getAffiliation()->isEmpty()
            && is_null($organisation->getParent())
            && $organisation->getParentFinancial()->isEmpty()
            && is_null($organisation->getParentOrganisation())
            && $organisation->getInvoice()->isEmpty()
            && $organisation->getBoothFinancial()->isEmpty()
            && $organisation->getOrganisationBooth()->isEmpty()
            && $organisation->getJournal()->isEmpty()
            && $organisation->getReminder()->isEmpty()
            && $organisation->getResult()->isEmpty();
    }


    /**
     * @param $id
     *
     * @return null|Entity\Organisation|object
     */
    public function findOrganisationById($id)
    {
        return $this->getEntityManager()->getRepository(Entity\Organisation::class)->find($id);
    }

    /**
     * @param Entity\Organisation $organisation
     *
     * @return string
     */
    public function parseDebtorNumber(Entity\Organisation $organisation): string
    {
        return trim(sprintf("%'.06d\n", 100000 + $organisation->getId()));
    }

    /**
     * @param Entity\Organisation $organisation
     *
     * @return string
     */
    public function parseCreditNumber(Entity\Organisation $organisation): string
    {
        return trim(sprintf("%'.06d\n", 200000 + $organisation->getId()));
    }

    /**
     * @param $filter
     *
     * @return Query
     */
    public function findActiveOrganisationWithoutFinancial($filter)
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findActiveOrganisationWithoutFinancial($filter);
    }

    /**
     * @param Result $result
     *
     * @return array
     */
    public function findOrganisationByResult(Result $result): array
    {
        //Create a list of organisations
        $organisations = [];

        //Now add the projects
        foreach ($result->getOrganisation() as $organisation) {
            $organisations[$organisation->getOrganisation()] = $organisation;
        }

        ksort($organisations);

        return $organisations;
    }

    /**
     * @param Entity\Organisation $organisation
     * @param string $organisationName
     * @param Project $project
     *
     * @return mixed|null|Entity\Name
     */
    public function findOrganisationNameByNameAndProject(
        Entity\Organisation $organisation,
        string $organisationName,
        Project $project
    ) {
        foreach ($organisation->getNames() as $name) {
            if ($name->getName() === $organisationName && $name->getProject() === $project) {
                return $name;
            }
        }

        return null;
    }

    /**
     * @param  Contact $contact
     *
     * @return Entity\Organisation[]
     */
    public function findOrganisationForProfileEditByContact(Contact $contact)
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationForProfileEditByContact($contact);
    }

    /**
     * @param Entity\Organisation $organisation
     * @param int $which
     *
     * @return int
     */
    public function getAffiliationCount(Entity\Organisation $organisation, $which = AffiliationService::WHICH_ALL)
    {
        return ($organisation->getAffiliation()->filter(
            function (
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
            }
        )->count());
    }

    /**
     * @param Entity\Organisation $organisation
     * @param int $which
     *
     * @return int
     */
    public function getContactCount(Entity\Organisation $organisation, $which = ContactService::WHICH_ONLY_ACTIVE)
    {
        return ($organisation->getContactOrganisation()->filter(
            function (
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
            }
        )->count());
    }

    /**
     * @param $filter
     *
     * @return Query
     */
    public function findOrganisationFinancialList($filter): Query
    {
        /** @var Repository\Financial $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Financial::class);

        return $repository->findOrganisationFinancialList($filter);
    }

    /**
     * @param Entity\Organisation $organisation
     *
     * @return Contact|\Contact\Entity\Selection|null|object
     */
    public function findFinancialContact(Entity\Organisation $organisation)
    {
        /**
         * The financial contact can be found be taking the contact which has the most invoices on his/her name
         */
        $invoiceContactList = [];
        foreach ($organisation->getInvoice() as $invoice) {
            $invoiceContactList[] = $invoice->getContact()->getId();
        }

        if (count($invoiceContactList) === 0) {
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
     * @return null|Entity\Organisation|object
     */
    public function findOrganisationByDocRef($docRef)
    {
        return $this->getEntityManager()->getRepository(Entity\Organisation::class)->findOneBy(['docRef' => $docRef]);
    }

    /**
     * @return Entity\Type[]
     */
    public function findOrganisationTypes()
    {
        return $this->getEntityManager()->getRepository(Entity\Type::class)->findBy([], ['type' => 'ASC']);
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
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findOrganisations($onlyActiveProject, $onlyActivePartner);
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
    public function findOrganisationByCountry(
        Country $country,
        $onlyActiveProject = true,
        $onlyActivePartner = true
    ) {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByCountry($country, $onlyActiveProject, $onlyActivePartner);
    }

    /**
     * @param  Entity\Organisation $organisation
     *
     * @return array
     */
    public function findBranchesByOrganisation(Entity\Organisation $organisation): array
    {
        //always include the <empty> branch
        $branches = ['' => (string)$organisation->getOrganisation()];

        foreach ($organisation->getContactOrganisation() as $contactOrganisation) {
            $branch = $contactOrganisation->getBranch();

            $branches[$branch] = $this->parseOrganisationWithBranch($branch, $organisation);
        }

        return array_unique($branches);
    }

    /**
     * @param                     $branch
     * @param Entity\Organisation $organisation = null
     *
     * @return string
     */
    public function parseOrganisationWithBranch(string $branch = null, Entity\Organisation $organisation): string
    {
        return self::parseBranch((string)$branch, $organisation);
    }

    /**
     * @param string $branch
     * @param Entity\Organisation $organisation
     * @return string
     */
    public static function parseBranch(string $branch = null, Entity\Organisation $organisation): string
    {
        if (is_string($branch) && strpos($branch, '!') === 0) {
            return substr($branch, 1);
        }

        return trim(preg_replace('/^(([^\~]*)\~\s?)?\s?(.*)$/', '${2}' . (string)$organisation . ' ${3}', $branch));
    }

    /**
     * Find a country based on three criteria: Name, CountryObject and the email address.
     *
     * @param string $name
     * @param Country $country
     * @param string $emailAddress
     *
     * @return Entity\Organisation[]
     */
    public function findOrganisationByNameCountryAndEmailAddress(
        $name,
        Country $country,
        $emailAddress
    ) {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByNameCountryAndEmailAddress($name, $country, $emailAddress);
    }

    /**
     * @param string $name
     * @param Country $country
     * @param int $typeId
     * @param string $email
     *
     * @return Entity\Organisation
     */
    public function createOrganisationFromNameCountryTypeAndEmail(
        string $name,
        Country $country,
        int $typeId,
        string $email
    ): Entity\Organisation {
        $organisation = new Entity\Organisation();
        $organisation->setOrganisation($name);
        $organisation->setCountry($country);
        $organisation->setType($this->findEntityById(Entity\Type::class, $typeId));
        /*
         * Add the domain in the saved domains for this new company
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($email);
        $organisationWeb = new Entity\Web();
        $organisationWeb->setOrganisation($organisation);
        $organisationWeb->setWeb($validateEmail->hostname);
        $organisationWeb->setMain(Entity\Web::MAIN);

        //Skip hostnames like yahoo, gmail and hotmail, outlook
        if (!in_array($organisation->getWeb(), ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'])) {
            $this->newEntity($organisationWeb);
        }

        return $organisation;
    }

    /**
     * @param $vat
     *
     * @return Entity\Financial|null|object
     */
    public function findFinancialOrganisationWithVAT($vat)
    {
        return $this->getEntityManager()->getRepository(Entity\Financial::class)->findOneBy(['vat' => $vat]);
    }

    /**
     * Find a country based on three criteria: Name, CountryObject.
     *
     * @param string $name
     * @param Country $country
     * @param bool $onlyMain
     *
     * @return Entity\Organisation
     */
    public function findOrganisationByNameCountry($name, Country $country, bool $onlyMain = true)
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByNameCountry($name, $country, $onlyMain);
    }


    /**
     * @param Meeting $meeting
     * @param Parameters $search
     *
     * @return Entity\Organisation[]
     */
    public function findOrganisationByMeetingAndDescriptionSearch(
        Meeting $meeting,
        Parameters $search
    ) {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByMeetingAndDescriptionSearch($meeting, $search);
    }

    /**
     * Produce a list of organisations for a project (only active).
     *
     * @param Project $project
     * @param bool $onlyActiveProject
     *
     * @return Entity\Organisation[]
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
                    '%s-%s',
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
     * @param Entity\Organisation $organisation
     * @param Program $program
     *
     * @return bool
     */
    public function hasDoaForProgram(Entity\Organisation $organisation, Program $program)
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
     * @return Entity\Organisation[]
     */
    public function searchOrganisation(
        $searchItem,
        $maxResults,
        $countryId = null,
        $onlyActiveProject = true,
        $onlyActivePartner = true
    ) {
        /** @var Repository\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Organisation::class);

        return $repository->searchOrganisations(
            $searchItem,
            $maxResults,
            $countryId,
            $onlyActiveProject,
            $onlyActivePartner
        );
    }
}
