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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Event\Entity\Meeting\Meeting;
use General\Entity\Country;
use Interop\Container\ContainerInterface;
use Organisation\Entity;
use Organisation\Repository;
use Organisation\Search\Service\OrganisationSearchService;
use Project\Entity\Project;
use Project\Entity\Result\Result;
use Project\Service\ProjectService;
use Search\Service\AbstractSearchService;
use Search\Service\SearchUpdateInterface;
use Solarium\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Update\Query\Document;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Stdlib\Parameters;
use Zend\Validator\EmailAddress;
use function array_count_values;
use function array_keys;
use function array_unique;
use function arsort;
use function count;
use function in_array;
use function ksort;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strpos;
use function substr;
use function trim;

/**
 * Class OrganisationService
 *
 * @package Organisation\Service
 */
class OrganisationService extends AbstractService implements SearchUpdateInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var OrganisationSearchService
     */
    private $organisationSearchService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container->get(EntityManager::class));

        $this->container = $container;
        $this->organisationSearchService = $container->get(OrganisationSearchService::class);
        $this->translator = $container->get(TranslatorInterface::class);
    }

    public static function determineBranch(string $givenName, string $organisation): string
    {
        //when the names are identical
        if ($givenName === $organisation) {
            return '';
        }

        /** When the name is not found in the organisation */
        if (strpos($givenName, $organisation) === false) {
            return sprintf('!%s', $givenName);
        }

        return str_replace($organisation, '~', $givenName);
    }

    public function canDeleteOrganisation(Entity\Organisation $organisation): bool
    {
        return
            $organisation->getContactOrganisation()->isEmpty()
            && $organisation->getAffiliation()->isEmpty()
            && null === $organisation->getParent()
            && $organisation->getParentFinancial()->isEmpty()
            && null === $organisation->getParentOrganisation()
            && $organisation->getInvoice()->isEmpty()
            && $organisation->getBoothFinancial()->isEmpty()
            && $organisation->getOrganisationBooth()->isEmpty()
            && $organisation->getJournal()->isEmpty()
            && $organisation->getReminder()->isEmpty()
            && $organisation->getResult()->isEmpty();
    }

    public function findOrganisationById(int $id): ?Entity\Organisation
    {
        return $this->entityManager->getRepository(Entity\Organisation::class)->find($id);
    }

    public function parseDebtorNumber(Entity\Organisation $organisation): string
    {
        return trim(sprintf("%'.06d\n", 100000 + $organisation->getId()));
    }

    public function parseCreditNumber(Entity\Organisation $organisation): string
    {
        return trim(sprintf("%'.06d\n", 200000 + $organisation->getId()));
    }

    public function findActiveOrganisationWithoutFinancial($filter): QueryBuilder
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findActiveOrganisationWithoutFinancial($filter);
    }

    public function findDuplicateOrganisations($filter): Query
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findDuplicateOrganisations($filter);
    }

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

    public function findOrganisationNameByNameAndProject(
        Entity\Organisation $organisation,
        string $organisationName,
        Project $project
    ): ?Entity\Name {
        foreach ($organisation->getNames() as $name) {
            if ($name->getName() === $organisationName && $name->getProject() === $project) {
                return $name;
            }
        }

        return null;
    }

    public function findOrganisationForProfileEditByContact(Contact $contact): array
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationForProfileEditByContact($contact);
    }

    public function getAffiliationCount(Entity\Organisation $organisation, $which = AffiliationService::WHICH_ALL): int
    {
        return $organisation->getAffiliation()->filter(
            function (
                Affiliation $affiliation
            ) use ($which) {
                switch ($which) {
                    case AffiliationService::WHICH_ONLY_ACTIVE:
                        return $affiliation->isActive();
                    case AffiliationService::WHICH_ONLY_INACTIVE:
                        return !$affiliation->isActive();
                    default:
                        return true;
                }
            }
        )->count();
    }

    public function findOrganisationFinancialList(array $filter): Query
    {
        /** @var Repository\Financial $repository */
        $repository = $this->entityManager->getRepository(Entity\Financial::class);

        return $repository->findOrganisationFinancialList($filter);
    }

    public function findFinancialContact(Entity\Organisation $organisation): ?Contact
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

        return $this->entityManager->find(Contact::class, (int)$contactId);
    }

    public function findOrganisationByDocRef(string $docRef)
    {
        return $this->entityManager->getRepository(Entity\Organisation::class)->findOneBy(['docRef' => $docRef]);
    }

    public function findOrganisationTypes(): array
    {
        return $this->entityManager->getRepository(Entity\Type::class)->findBy([], ['type' => 'ASC']);
    }

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

    public function parseOrganisationWithBranch(?string $branch, Entity\Organisation $organisation): string
    {
        return self::parseBranch($branch, $organisation);
    }

    public static function parseBranch(?string $branch, Entity\Organisation $organisation): string
    {
        if (null !== $branch && strpos($branch, '!') === 0) {
            return substr($branch, 1);
        }

        return trim(preg_replace('/^(([^\~]*)\~\s?)?\s?(.*)$/', '${2}' . $organisation . ' ${3}', $branch));
    }

    public function removeInactiveOrganisations(): void
    {
        $inactiveOrganisations = $this->findInactiveOrganisations();
        foreach ($inactiveOrganisations as $inactiveOrganisation) {
            $this->delete($inactiveOrganisation);
        }
    }

    public function findInactiveOrganisations(): array
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findInactiveOrganisations();
    }

    public function delete(Entity\AbstractEntity $abstractEntity): void
    {
        if ($abstractEntity instanceof Entity\Organisation) {
            $this->organisationSearchService->deleteDocument($abstractEntity);
        }

        parent::delete($abstractEntity);
    }

    public function findOrganisationByNameCountryAndEmailAddress(
        string $name,
        Country $country,
        string $emailAddress
    ): array {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByNameCountryAndEmailAddress($name, $country, $emailAddress);
    }

    public function createOrganisationFromNameCountryTypeAndEmail(
        string $name,
        Country $country,
        int $typeId,
        string $email
    ): Entity\Organisation {
        $organisation = new Entity\Organisation();
        $organisation->setOrganisation($name);
        $organisation->setCountry($country);

        /** @var Entity\Type $type */
        $type = $this->find(Entity\Type::class, (int)$typeId);

        $organisation->setType($type);
        /*
         * Add the domain in the saved domains for this new company
         * Use the ZF2 EmailAddress validator to strip the hostname out of the EmailAddress
         */
        $validateEmail = new EmailAddress();
        $validateEmail->isValid($email);
        $organisationWeb = new Entity\Web();
        $organisationWeb->setOrganisation($organisation);
        $organisationWeb->setWeb('http://' . $validateEmail->hostname);
        $organisationWeb->setMain(Entity\Web::MAIN);

        //Skip hostnames like yahoo, gmail and hotmail, outlook
        if (!in_array($validateEmail->hostname, ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'], true)) {
            $this->save($organisationWeb);
        }

        return $organisation;
    }

    public function save(Entity\AbstractEntity $abstractEntity): Entity\AbstractEntity
    {
        parent::save($abstractEntity);

        if ($abstractEntity instanceof Entity\Organisation) {
            $this->updateEntityInSearchEngine($abstractEntity);
        }

        return $abstractEntity;
    }

    /**
     * @param Entity\Organisation $organisation
     */
    public function updateEntityInSearchEngine($organisation): void
    {
        $document = $this->prepareSearchUpdate($organisation);

        $this->organisationSearchService->executeUpdateDocument($document);
    }

    /**
     * @param Entity\Organisation $organisation
     *
     * @return AbstractQuery
     */
    public function prepareSearchUpdate($organisation): AbstractQuery
    {
        $searchClient = new Client();
        $update = $searchClient->createUpdate();

        /** @var Document $organisationDocument */
        $organisationDocument = $update->createDocument();

        // Organisation properties
        $organisationDocument->setField('id', $organisation->getResourceId());
        $organisationDocument->setField('organisation_id', $organisation->getId());
        $organisationDocument->setField('organisation', $organisation->getOrganisation());
        $organisationDocument->setField('organisation_sort', $organisation->getOrganisation());
        $organisationDocument->setField('organisation_search', $organisation->getOrganisation());
        $organisationDocument->setField('organisation_docref', $organisation->getDocRef());

        if (null !== $organisation->getDescription()) {
            $organisationDocument->setField('description', $organisation->getDescription()->getDescription());
            $organisationDocument->setField('description_search', $organisation->getDescription()->getDescription());
        }

        $organisationDocument->setField('organisation_type', $organisation->getType()->getType());
        $organisationDocument->setField('organisation_type_sort', $organisation->getType()->getType());
        $organisationDocument->setField('organisation_type_description', $organisation->getType()->getDescription());
        $organisationDocument->setField('organisation_type_search', $organisation->getType()->getType());

        $organisationDocument->setField('country', $organisation->getCountry()->getCountry());
        $organisationDocument->setField('country_sort', $organisation->getCountry()->getCountry());
        $organisationDocument->setField('country_search', $organisation->getCountry()->getCountry());

        if (null !== $organisation->getParentOrganisation()) {
            $parentOrganisation = $organisation->getParentOrganisation();
            $organisationDocument->setField('parent_id', $parentOrganisation->getParent()->getId());
            $organisationDocument->setField(
                'parent',
                $parentOrganisation->getParent()->getOrganisation()->getOrganisation()
            );
            $organisationDocument->setField(
                'parent_sort',
                $parentOrganisation->getParent()->getOrganisation()->getOrganisation()
            );
            $organisationDocument->setField(
                'parent_search',
                $parentOrganisation->getParent()->getOrganisation()->getOrganisation()
            );
        }

        if (null !== $organisation->getFinancial() && !empty($organisation->getFinancial()->getVat())) {
            $organisationDocument->setField('vat', $organisation->getFinancial()->getVat());
            $organisationDocument->setField('vat_search', $organisation->getFinancial()->getVat());
        }

        if (null !== $organisation->getDateCreated()) {
            $organisationDocument->setField(
                'date_created',
                $organisation->getDateCreated()->format(
                    AbstractSearchService::DATE_SOLR
                )
            );
        }
        if (null !== $organisation->getDateUpdated()) {
            $organisationDocument->setField(
                'date_updated',
                $organisation->getDateUpdated()->format(
                    AbstractSearchService::DATE_SOLR
                )
            );
        }

        //Find all the projects and partners

        $projectsOnWebsite = [];
        foreach ($organisation->getAffiliation() as $affiliation) {
            if (!$affiliation->isActive()) {
                continue;
            }

            $project = $affiliation->getProject();
            if (!$this->getProjectService()->onWebsite($project)) {
                continue;
            }

            $projectId = $project->getId();
            $projectsOnWebsite[$projectId] = $projectId;
        }

        $projects = [];
        $affiliations = [];

        foreach ($organisation->getAffiliation() as $affiliation) {
            $project = $affiliation->getProject();

            $projectId = $project->getId();
            $affiliationId = $affiliation->getId();

            $projects[$projectId] = $projectId;
            $affiliations[$affiliationId] = $affiliationId;
        }

        $organisationDocument->setField('projects', count($projects));
        $organisationDocument->setField('projects_on_website', count($projectsOnWebsite));

        $organisationDocument->setField('affiliations', count($affiliations));
        $organisationDocument->setField('invoices', $organisation->getInvoice()->count());
        $organisationDocument->setField(
            'parent_organisations',
            null !== $organisation->getParent() ? $organisation->getParent()->getParentOrganisation()->count() : 0
        );

        $organisationDocument->setField('contacts', $this->getContactCount($organisation, ContactService::WHICH_ALL));
        $organisationDocument->setField('active_contacts', $this->getContactCount($organisation));
        $organisationDocument->setField(
            'inactive_contacts',
            $this->getContactCount($organisation, ContactService::WHICH_ONLY_EXPIRED)
        );

        $organisationDocument->setField('has_projects', count($projects) > 0);
        $organisationDocument->setField(
            'has_projects_text',
            count($projects) > 0 ? $this->translator->translate('txt-yes') : $this->translator->translate('txt-no')
        );

        $organisationDocument->setField('has_projects_on_website', count($projectsOnWebsite) > 0);
        $organisationDocument->setField(
            'has_projects_on_website_text',
            count($projectsOnWebsite) > 0
                ? $this->translator->translate('txt-yes')
                : $this->translator->translate(
                    'txt-no'
                )
        );

        $organisationDocument->setField('is_parent', $organisation->isParent());
        $organisationDocument->setField(
            'is_parent_text',
            $organisation->isParent() ? $this->translator->translate('txt-yes')
                : $this->translator->translate('txt-no')
        );
        if ($organisation->isParent()) {
            $organisationDocument->setField('own_parent_id', $organisation->getParent()->getId());
        }

        $organisationDocument->setField('has_parent', $organisation->hasParent());
        $organisationDocument->setField(
            'has_parent_text',
            $organisation->hasParent() ? $this->translator->translate('txt-yes')
                : $this->translator->translate('txt-no')
        );

        if ($organisation->isParent()) {
            $organisationDocument->setField(
                'has_wrong_parent_child_relationship',
                ParentService::hasWrongParentChildRelationship($organisation->getParent())
            );
            $organisationDocument->setField(
                'has_wrong_parent_child_relationship_text',
                ParentService::hasWrongParentChildRelationship($organisation->getParent())
                    ? $this->translator->translate('txt-yes')
                    : $this->translator->translate('txt-no')
            );
        }

        $isOwnParent = null !== $organisation->getParent()
            && $organisation->getParent()->getOrganisation() === $organisation;

        $organisationDocument->setField('is_own_parent', $isOwnParent);
        $organisationDocument->setField(
            'is_own_parent_text',
            $isOwnParent ? $this->translator->translate('txt-yes') : $this->translator->translate('txt-no')
        );

        $organisationDocument->setField('has_financial', null !== $organisation->getFinancial());
        $organisationDocument->setField(
            'has_financial_text',
            null !== $organisation->getFinancial() ? $this->translator->translate('txt-yes')
                : $this->translator->translate('txt-no')
        );

        $organisationDocument->setField('has_affiliations', count($affiliations) > 0);
        $organisationDocument->setField(
            'has_affiliations_text',
            count($affiliations) > 0 ? $this->translator->translate('txt-yes') : $this->translator->translate('txt-no')
        );

        $organisationDocument->setField(
            'has_contacts',
            $this->getContactCount($organisation, ContactService::WHICH_ALL) > 0
        );
        $organisationDocument->setField(
            'has_contacts_text',
            $this->getContactCount($organisation, ContactService::WHICH_ALL) > 0 ? $this->translator->translate(
                'txt-yes'
            ) : $this->translator->translate('txt-no')
        );

        $organisationDocument->setField('has_invoices', !$organisation->getInvoice()->isEmpty());
        $organisationDocument->setField(
            'has_invoices_text',
            !$organisation->getInvoice()->isEmpty() ? $this->translator->translate('txt-yes')
                : $this->translator->translate('txt-no')
        );


        $update->addDocument($organisationDocument);
        $update->addCommit();

        return $update;
    }

    private function getProjectService(): ProjectService
    {
        return $this->container->get(ProjectService::class);
    }

    public function getContactCount(Entity\Organisation $organisation, $which = ContactService::WHICH_ONLY_ACTIVE): int
    {
        return $organisation->getContactOrganisation()->filter(
            function (
                ContactOrganisation $contactOrganisation
            ) use (
                $which
            ) {
                switch ($which) {
                    case ContactService::WHICH_ONLY_ACTIVE:
                        return $contactOrganisation->getContact()->isActive();
                    case ContactService::WHICH_ONLY_EXPIRED:
                        return !$contactOrganisation->getContact()->isActive();
                    default:
                        return true;
                }
            }
        )->count();
    }

    public function findFinancialOrganisationWithVAT(string $vat): ?Entity\Financial
    {
        return $this->entityManager->getRepository(Entity\Financial::class)->findOneBy(['vat' => $vat]);
    }

    public function findOrganisationByNameCountry(
        string $name,
        Country $country,
        bool $onlyMain = true
    ): ?Entity\Organisation {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByNameCountry($name, $country, $onlyMain);
    }

    /**
     * @param string  $name
     * @param Country $country
     * @param bool    $onlyMain
     *
     * @return Entity\Organisation[]
     */
    public function findOrganisationsByNameCountry(string $name, Country $country, bool $onlyMain = true)
    {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationsByNameCountry($name, $country, $onlyMain);
    }

    public function findOrganisationByMeetingAndDescriptionSearch(
        Meeting $meeting,
        Parameters $search
    ): array {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);

        return $repository->findOrganisationByMeetingAndDescriptionSearch($meeting, $search);
    }

    public function findOrganisationByProject(
        Project $project,
        bool $onlyActiveProject = true
    ): array {
        $organisations = [];
        foreach ($project->getAffiliation() as $affiliation) {
            if ($onlyActiveProject && $affiliation->isActive()) {
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

    public function hasDoaForProgram(Affiliation $affiliation): bool
    {
        //When the organisation has a DOA on parent level, check that first
        if (null !== $affiliation->getParentOrganisation()) {
            foreach ($affiliation->getParentOrganisation()->getParent()->getDoa() as $doa) {
                if ($doa->getProgram()->getId() === $affiliation->getProject()->getCall()->getProgram()->getId()) {
                    return true;
                }
            }
        }

        foreach ($affiliation->getOrganisation()->getProgramDoa() as $doa) {
            if ($doa->getProgram()->getId() === $affiliation->getProject()->getCall()->getProgram()->getId()) {
                return true;
            }
        }

        return false;
    }

    public function hasValidVat(Entity\Organisation $organisation): bool
    {
        if (null === $organisation->getFinancial()) {
            return false;
        }

        return $organisation->getFinancial()->getVatStatus() === Entity\Financial::VAT_STATUS_VALID;
    }

    public function updateCollectionInSearchEngine(bool $clearIndex = false): void
    {
        $organisationItems = $this->findAll(Entity\Organisation::class);
        $collection = [];

        /** @var Entity\Organisation $organisation */
        foreach ($organisationItems as $organisation) {
            $collection[] = $this->prepareSearchUpdate($organisation);
        }

        $this->organisationSearchService->updateIndexWithCollection($collection, $clearIndex);
    }

    public function searchOrganisation(
        string $searchItem,
        int $maxResults = 20
    ) {
        /** @var Repository\Organisation $repository */
        $repository = $this->entityManager->getRepository(Entity\Organisation::class);
        return $repository->searchOrganisations(
            $searchItem,
            $maxResults
        );
    }
}
