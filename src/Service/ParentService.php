<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Service;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Interop\Container\ContainerInterface;
use Invoice\Entity\Method;
use Organisation\Entity;
use Organisation\Repository;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Project\Entity\Version\Version;
use Project\Service\ProjectService;
use Project\Service\VersionService;

use function abs;
use function array_merge;
use function count;
use function in_array;
use function round;

/**
 * Class ParentService
 *
 * @package Parent\Service
 */
class ParentService extends AbstractService
{
    public const PARENT_INVOICE_FACTOR = 1.7;
    public const DOA_INVOICE_FACTOR    = 1.9;

    private ContainerInterface $container;
    private ProjectService $projectService;
    private VersionService $versionService;

    /**
     * Because of circular dependencies between parentService and AffiliationService we choose here to use an invokable
     *
     * @var ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container->get(EntityManager::class));

        $this->projectService = $container->get(ProjectService::class);
        $this->versionService = $container->get(VersionService::class);

        $this->container = $container;
    }

    public static function hasWrongParentChildRelationship(Entity\ParentEntity $parent): bool
    {
        return null !== $parent->getOrganisation()->getParentOrganisation()
            && $parent->getOrganisation()->getParentOrganisation()->getParent()->getId() !== $parent->getId();
    }

    public function findParentById(int $id): ?Entity\ParentEntity
    {
        return $this->entityManager->find(Entity\ParentEntity::class, $id);
    }

    public function findParentByOrganisationName(string $name): ?Entity\ParentEntity
    {
        return $this->entityManager->getRepository(Entity\ParentEntity::class)->findParentByOrganisationName($name);
    }

    public function findParentTypeByName(string $name): ?Entity\Parent\Type
    {
        return $this->entityManager->getRepository(Entity\Parent\Type::class)->findOneBy(['type' => $name]);
    }

    public function findParents(): ArrayCollection
    {
        return new ArrayCollection(
            $this->entityManager->getRepository(Entity\ParentEntity::class)->findAll()
        );
    }

    public function canDeleteParent(Entity\ParentEntity $parent): bool
    {
        return $parent->getParentOrganisation()->isEmpty() && $parent->getInvoice()->isEmpty()
            && $parent->getInvoiceExtra()->isEmpty();
    }

    public function canDeleteType(Entity\Parent\Type $type): bool
    {
        $cannotDeleteType = [];

        if (! $type->getParent()->isEmpty()) {
            $cannotDeleteType[] = 'This type still has parents';
        }

        return count($cannotDeleteType) === 0;
    }

    public function findActiveParentWhichAreNoMember(array $filter): QueryBuilder
    {
        $repository = $this->entityManager->getRepository(Entity\ParentEntity::class);

        return $repository->findActiveParentWhichAreNoMember($filter);
    }

    public function findActiveParentWithoutFinancial(array $filter): QueryBuilder
    {
        /** @var Repository\ParentRepository $repository */
        $repository = $this->entityManager->getRepository(Entity\ParentEntity::class);

        return $repository->findActiveParentWithoutFinancial($filter);
    }

    public function createParentAndParentOrganisationFromOrganisation(
        Entity\Organisation $organisation,
        Contact $contact
    ): Entity\Parent\Organisation {

        //If the organisation is already a parent
        if (null !== $organisation->getParent()) {
            $parentOrganisation = $this->findParentOrganisationInParentByOrganisation(
                $organisation->getParent(),
                $organisation
            );

            if (null !== $parentOrganisation) {
                return $parentOrganisation;
            }

            //we have the parent now, but cannot find the organisation, so we create it.
            $parentOrganisation = new Entity\Parent\Organisation();
            $parentOrganisation->setOrganisation($organisation);
            $parentOrganisation->setParent($organisation->getParent());
            $parentOrganisation->setContact($contact);
            $this->save($parentOrganisation);

            return $parentOrganisation;
        }

        //If the organisation has already a parent
        if (null !== $organisation->getParentOrganisation()) {
            $parent = $organisation->getParentOrganisation()->getParent();

            $parentOrganisation = $this->findParentOrganisationInParentByOrganisation($parent, $organisation);

            if (null !== $parentOrganisation) {
                return $parentOrganisation;
            }

            //we have the parent now, but cannot find the organisation, so we create it.
            $parentOrganisation = new Entity\Parent\Organisation();
            $parentOrganisation->setOrganisation($organisation);
            $parentOrganisation->setParent($parent);
            $parentOrganisation->setContact($contact);
            $this->save($parentOrganisation);

            return $parentOrganisation;
        }

        //We have no parent so create it all
        $parent = new Entity\ParentEntity();
        $parent->setOrganisation($organisation);
        $parent->setContact($contact);
        /** @var Entity\Parent\Type $type */
        $type = $this->find(Entity\Parent\Type::class, Entity\Parent\Type::TYPE_OTHER);
        $parent->setType($type);

        $this->save($parent);

        //we have the parent now, but cannot find the organisation, so we create it.
        $parentOrganisation = new Entity\Parent\Organisation();
        $parentOrganisation->setOrganisation($organisation);
        $parentOrganisation->setParent($parent);
        $parentOrganisation->setContact($contact);
        $this->save($parentOrganisation);

        return $parentOrganisation;
    }

    public function findParentOrganisationInParentByOrganisation(
        Entity\ParentEntity $parent,
        Entity\Organisation $organisation
    ): ?Entity\Parent\Organisation {
        foreach ($parent->getParentOrganisation() as $parentOrganisation) {
            if ($parentOrganisation->getOrganisation() === $organisation) {
                return $parentOrganisation;
            }
        }

        return null;
    }

    public function parseTotalFundedByParent(Entity\ParentEntity $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $totalFunded = 0;
        foreach (
            $this->findAffiliationByParentAndProgramAndWhich(
                $parent,
                $program,
                AffiliationService::WHICH_INVOICING,
                $year
            ) as $affiliation
        ) {
            $latestVersion = $this->projectService->getLatestApprovedProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $totalFunded += $this->versionService->findTotalFundingVersionByAffiliationAndVersion(
                    $affiliation,
                    $latestVersion
                );
            }
        }

        return (float)$totalFunded;
    }

    public function findAffiliationByParentAndProgramAndWhich(
        Entity\ParentEntity $parent,
        Program $program,
        int $which = AffiliationService::WHICH_ONLY_ACTIVE,
        ?int $year = null
    ): ArrayCollection {
        /** @var \Affiliation\Repository\Affiliation $repository */
        $repository   = $this->entityManager->getRepository(Affiliation::class);
        $affiliations = $repository->findAffiliationByParentAndProgramAndWhich($parent, $program, $which, $year);

        if (null === $affiliations) {
            $affiliations = [];
        }

        return new ArrayCollection($affiliations);
    }

    public function parseContribution(Entity\ParentEntity $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contribution = 0;
        foreach (
            $this->findAffiliationByParentAndProgramAndWhich(
                $parent,
                $program,
                AffiliationService::WHICH_INVOICING,
                $year
            ) as $affiliation
        ) {
            $latestVersion = $this->projectService->getLatestApprovedProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $contribution += $this->getAffiliationService()->parseContribution(
                    $affiliation,
                    $latestVersion,
                    null,
                    $year
                );
            }
        }

        return (float)$contribution;
    }

    private function getAffiliationService(): AffiliationService
    {
        return $this->container->get(AffiliationService::class);
    }


    public function parseTotalExtraVariableBalanceByParent(Entity\ParentEntity $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $balanceTotal = 0;

        foreach (
            $this->projectService->findProjectsByParent(
                $parent,
                $program,
                AffiliationService::WHICH_INVOICING,
                $year
            ) as $project
        ) {
            $version = $this->projectService->getLatestApprovedProjectVersion($project);

            //Only add the balance when there is a version
            if (null !== $version && $this->hasExtraVariableBalanceByParentAndVersion($parent, $version)) {
                $value = $this->parseExtraVariableBalanceByParentAndVersion($parent, $version);

                $balanceTotal += $value;
            }
        }

        return round($balanceTotal);
    }

    public function hasExtraVariableBalanceByParentAndVersion(Entity\ParentEntity $parent, Version $version): bool
    {
        return abs($this->parseExtraVariableBalanceByParentAndVersion($parent, $version)) > 0;
    }

    public function parseExtraVariableBalanceByParentAndVersion(Entity\ParentEntity $parent, Version $version): float
    {
        /**
         * The formula is
         *
         * 1.8% * SUM FREE RIDERS * FUNDING BY C CHAMBER / 3 * MEMBERSHIPS * SUM OF FUNDING OF ALL C CHAMBERS
         *
         */
        $sumOfFreeRiders         = $this->versionService->findTotalFundingVersionByFreeRidersInVersion($version);
        $sumOfFundingByCChamber  = $this->versionService->findTotalFundingVersionByParentAndVersion($parent, $version);
        $sumOfFundingByCChambers = $this->versionService->findTotalFundingVersionByCChambersInVersion($version);
        $amountOfMemberships     = $this->parseMembershipFactor($parent);

        if ($amountOfMemberships === 0 || $sumOfFundingByCChambers < 0.001) {
            return (float)0;
        }

        return ((self::PARENT_INVOICE_FACTOR / 100) * $sumOfFreeRiders * $sumOfFundingByCChamber) / (3 * $amountOfMemberships
                * $sumOfFundingByCChambers);
    }

    public function parseMembershipFactor(Entity\ParentEntity $parent): int
    {
        return count($this->parseMemberships($parent));
    }

    public function parseMemberships(Entity\ParentEntity $parent): array
    {
        $memberships = [];

        if ($parent->getMemberType() === Entity\ParentEntity::MEMBER_TYPE_MEMBER) {
            $memberships[] = 'AENEAS';
        }
        if ($parent->getArtemisiaMemberType() === Entity\ParentEntity::ARTEMISIA_MEMBER_TYPE_MEMBER) {
            $memberships[] = 'ARTEMIS-IA';
        }
        if ($parent->getEpossMemberType() === Entity\ParentEntity::EPOSS_MEMBER_TYPE_MEMBER) {
            $memberships[] = 'EPOSS';
        }

        return $memberships;
    }

    public function parseDoaFactor(Entity\ParentEntity $parent, Program $program = null): int
    {
        return count($this->parseDoas($parent, $program));
    }

    public function parseDoas(Entity\ParentEntity $parent, ?Program $program = null): array
    {
        $otherDoa = [];

        foreach ($parent->getDoa() as $doa) {
            if (null === $program || $doa->getProgram()->getId() === $program->getId()) {
                $otherDoa[] = $doa->getProgram()->getProgram();
            }
        }

        if ($parent->getArtemisiaMemberType() === Entity\ParentEntity::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER) {
            $otherDoa[] = 'ARTEMIS-IA';
        }
        if ($parent->getEpossMemberType() === Entity\ParentEntity::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER) {
            $otherDoa[] = 'EPOSS';
        }

        return $otherDoa;
    }

    public function renderProjectsByParentInYear(Entity\ParentEntity $parent, Program $program, int $year): array
    {
        //Sort the projects per call
        $projects = [];
        foreach (
            $this->findAffiliationByParentAndProgramAndWhich(
                $parent,
                $program,
                AffiliationService::WHICH_INVOICING,
                $year
            ) as $affiliation
        ) {
            /** @var Call $call */
            $call = $affiliation->getProject()->getCall();

            //Initialize the array
            if (! array_key_exists($call->getId(), $projects)) {
                $projects[$call->getId()]['affiliation']       = [];
                $projects[$call->getId()]['call']              = $call;
                $projects[$call->getId()]['totalFunding']      = 0;
                $projects[$call->getId()]['totalContribution'] = 0;
            }

            $latestVersion = $this->projectService->getLatestApprovedProjectVersion($affiliation->getProject());

            //Skip the rest of the calculation if a project has no version
            if (null === $latestVersion) {
                continue;
            }

            $funding      = $this->versionService->findTotalFundingVersionByAffiliationAndVersion(
                $affiliation,
                $latestVersion
            );
            $contribution = $this->getAffiliationService()->parseContribution(
                $affiliation,
                $latestVersion,
                null,
                $year
            );

            $projects[$call->getId()]['affiliation'][] = [
                'affiliation'  => $affiliation,
                'funding'      => $funding,
                'contribution' => $contribution
            ];


            $projects[$call->getId()]['totalFunding']      += $funding;
            $projects[$call->getId()]['totalContribution'] += $contribution;
        }

        return $projects;
    }

    public function parseInvoiceFactor(Entity\ParentEntity $parent, Program $program): float
    {
        //If the invoice method is based on the METHOD::METHOD_FUNDING
        if (
            ! $program->getInvoiceMethod()->isEmpty()
            && $program->getInvoiceMethod()->first()->getId() === Method::METHOD_FUNDING
        ) {
            if ($parent->isMember() || $this->hasDoaForProgram($parent, $program)) {
                return self::PARENT_INVOICE_FACTOR;
            }

            return 0;
        }

        if ($parent->isMember()) {
            return self::PARENT_INVOICE_FACTOR;
        }

        //If the organisation is member of any other organisation we will not invoice
        if (! $this->hasDoaForProgram($parent, $program) || $this->hasOtherMemberships($parent)) {
            return 0;
        }

        return self::DOA_INVOICE_FACTOR;
    }

    public function hasDoaForProgram(Entity\ParentEntity $parent, Program $program): bool
    {
        foreach ($parent->getDoa() as $doa) {
            if ($doa->getProgram()->getId() === $program->getId()) {
                return true;
            }
        }

        return false;
    }

    public function hasOtherMemberships(Entity\ParentEntity $parent): bool
    {
        if ($parent->getArtemisiaMemberType() === Entity\ParentEntity::ARTEMISIA_MEMBER_TYPE_MEMBER) {
            return true;
        }
        if ($parent->getEpossMemberType() === Entity\ParentEntity::EPOSS_MEMBER_TYPE_MEMBER) {
            return true;
        }

        return false;
    }

    public function parseTotal(
        Entity\ParentEntity $parent,
        Program $program,
        int $year,
        array $includeAffiliations = null
    ): float {
        //Go over each affiliation and sum up what has been paid already
        $contributionTotal = 0;

        /** @var Affiliation $affiliation */
        foreach (
            $this->findAffiliationByParentAndProgramAndWhich(
                $parent,
                $program,
                AffiliationService::WHICH_INVOICING,
                $year
            ) as $affiliation
        ) {
            //Skip the affiliations which are not in the $include affiliations table
            if (null !== $includeAffiliations && ! in_array($affiliation, $includeAffiliations, true)) {
                continue;
            }

            $latestVersion = $this->projectService->getLatestApprovedProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $contributionTotal += $this->getAffiliationService()->parseTotal(
                    $affiliation,
                    $latestVersion,
                    $year
                );
            }
        }

        //Parent invoices are always rounded on 2 digits
        return round($contributionTotal, 0);
    }

    public function findActiveParents(): ArrayCollection
    {
        $repository = $this->entityManager->getRepository(Entity\ParentEntity::class);

        return new ArrayCollection($repository->findActiveParents());
    }

    public function findParentOrganisationByNameLike(string $name): ArrayCollection
    {
        /** @var Repository\Parent\OrganisationRepository $repository */
        $repository = $this->entityManager->getRepository(Entity\Parent\Organisation::class);

        return new ArrayCollection($repository->findParentOrganisationByNameLike($name));
    }

    public function findParentsForInvoicing(Program $program): ArrayCollection
    {
        /** @var Repository\ParentRepository $repository */
        $repository = $this->entityManager->getRepository(Entity\ParentEntity::class);

        return new ArrayCollection($repository->findParentsForInvoicing($program));
    }

    public function findParentsForExtraInvoicing(): ArrayCollection
    {
        /** @var Repository\ParentRepository $repository */
        $repository = $this->entityManager->getRepository(Entity\ParentEntity::class);

        return new ArrayCollection($repository->findParentsForExtraInvoicing());
    }

    public function getFinancialContact(Entity\ParentEntity $parent): ?Contact
    {
        if ($parent->getFinancial()->count() === 1) {
            return $parent->getFinancial()->first()->getContact();
        }

        return null;
    }

    public function findAllParentExtraInvoiceByParentYear(
        Entity\ParentEntity $parent,
        int $year,
        Program $program
    ): array {
        return array_merge(
            $this->findParentInvoiceByParentYear($parent, $year, $program)->toArray(),
            $this->findParentExtraInvoiceByParentYear($parent, $year)->toArray()
        );
    }

    public function findParentInvoiceByParentYear(Entity\ParentEntity $parent, int $year, Program $program): ArrayCollection
    {
        return $parent->getInvoice()->filter(
            static function (Entity\Parent\Invoice $invoice) use ($year, $program) {
                return $invoice->getYear() === $year && $invoice->getProgram() === $program;
            }
        );
    }

    public function findParentExtraInvoiceByParentYear(
        Entity\ParentEntity $parent,
        int $year
    ): ArrayCollection {
        return $parent->getInvoiceExtra()->filter(
            static function (Entity\Parent\InvoiceExtra $invoiceExtra) use ($year) {
                return $invoiceExtra->getYear() === $year;
            }
        );
    }

    public function canDeleteParentOrganisation(Entity\Parent\Organisation $organisation): bool
    {
        return $organisation->getAffiliation()->isEmpty();
    }

    public function canCreateInvoice(Entity\ParentEntity $parent, bool $autoGenerate = false): ArrayCollection
    {
        $errors = [];
        switch (true) {
            case $parent->getFinancial()->count() === 0:
                $errors[] = 'No financial organisation (parent financial) set for this parent';
                break;
            case null !== $parent->getDateEnd():
                $errors[] = 'Parent is de-activated';
                break;
            case ! $autoGenerate && ! empty($parent->getFinancial()) && $parent->getFinancial()->count() !== 1:
                $errors[] = 'More than 1 financial organisation known';
                break;
            default:
                foreach ($parent->getFinancial() as $financial) {
                    if (null === $financial->getOrganisation()->getFinancial()) {
                        $errors[] = sprintf('%s has no financial information', $financial->getOrganisation());
                    }

                    if (
                        null !== $financial->getOrganisation()->getFinancial()
                        && empty(
                            $financial->getOrganisation()->getFinancial()->getVat()
                        )
                    ) {
                        $errors[] = sprintf('%s has no VAT number', $financial->getOrganisation());
                    }

                    if (
                        null !== $financial->getOrganisation()->getFinancial()
                        && ! empty(
                            $financial->getOrganisation()->getFinancial()->getVat()
                        )
                        && $financial->getOrganisation()->getFinancial()->getVatStatus()
                        !== Entity\Financial::VAT_STATUS_VALID
                    ) {
                        $errors[] = sprintf('%s has an unvalidated VAT number', $financial->getOrganisation());
                    }
                }
                break;
        }

        return new ArrayCollection($errors);
    }

    public function searchParent(
        string $searchItem,
        int $maxResults = 20
    ): array {
        /** @var Repository\ParentRepository $repository */
        $repository = $this->entityManager->getRepository(Entity\ParentEntity::class);
        return $repository->searchParents(
            $searchItem,
            $maxResults
        );
    }
}
