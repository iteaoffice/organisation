<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Service;

use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Organisation\Entity;
use Organisation\Repository;
use Program\Entity\Call\Call;
use Program\Entity\Program;
use Project\Entity\Version\Version;

/**
 * Class ParentService
 *
 * @package Parent\Service
 */
class ParentService extends AbstractService
{
    /**
     * @param $id
     *
     * @return null|Entity\OParent|object
     */
    public function findParentById($id): ?Entity\OParent
    {
        return $this->findEntityById(Entity\OParent::class, $id);
    }

    /**
     * @param string $name
     *
     * @return null|Entity\Parent\Status
     */
    public function findParentStatusByName(string $name): ?Entity\Parent\Status
    {
        return $this->getEntityManager()->getRepository(Entity\Parent\Status::class)->findOneBy(['status' => $name]);
    }

    /**
     * @param string $name
     *
     * @return null|Entity\Parent\Type
     */
    public function findParentTypeByName(string $name): ?Entity\Parent\Type
    {
        return $this->getEntityManager()->getRepository(Entity\Parent\Type::class)->findOneBy(['type' => $name]);
    }


    /**
     * @return ArrayCollection|Entity\Organisation[]
     */
    public function findParents(): ArrayCollection
    {
        return new ArrayCollection($this->getEntityManager()->getRepository(Entity\OParent::class)
            ->findAll());
    }

    /**
     * @param Entity\OParent $parent
     * @return bool
     */
    public function parentCanBeDeleted(Entity\OParent $parent): bool
    {
        return $parent->getParentOrganisation()->isEmpty() && $parent->getInvoice()->isEmpty();
    }

    /**
     * @param array $filter
     * @return Query
     */
    public function findActiveParentWhichAreNoMember(array $filter): Query
    {
        /** @var Repository\OParent $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\OParent::class);

        return $repository->findActiveParentWhichAreNoMember($filter);
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @return bool
     */
    public function hasDoaForProgram(Entity\OParent $parent, Program $program): bool
    {
        foreach ($parent->getDoa() as $doa) {
            if ($doa->getProgram()->getId() === $program->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $filter
     * @return Query
     */
    public function findActiveParentWithoutFinancial(array $filter): Query
    {
        /** @var Repository\OParent $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\OParent::class);

        return $repository->findActiveParentWithoutFinancial($filter);
    }

    /**
     * @param Entity\Organisation $organisation
     * @param Contact $contact
     *
     * @return Entity\Parent\Organisation
     */
    public function createParentAndParentOrganisationFromOrganisation(
        Entity\Organisation $organisation,
        Contact $contact
    ): Entity\Parent\Organisation {

        //If the organisation is already a parent
        if (!\is_null($organisation->getParent())) {
            $parentOrganisation = $this->findParentOrganisationInParentByOrganisation(
                $organisation->getParent(),
                $organisation
            );

            if (!\is_null($parentOrganisation)) {
                return $parentOrganisation;
            }

            //we have the parent now, but cannot find the organisation, so we create it.
            $parentOrganisation = new Entity\Parent\Organisation();
            $parentOrganisation->setOrganisation($organisation);
            $parentOrganisation->setParent($organisation->getParent());
            $parentOrganisation->setContact($contact);
            $this->newEntity($parentOrganisation);

            return $parentOrganisation;
        }

        //If the organisation has already a parent
        if (!\is_null($organisation->getParentOrganisation())) {
            $parent = $organisation->getParentOrganisation()->getParent();

            $parentOrganisation = $this->findParentOrganisationInParentByOrganisation($parent, $organisation);

            if (!\is_null($parentOrganisation)) {
                return $parentOrganisation;
            }

            //we have the parent now, but cannot find the organisation, so we create it.
            $parentOrganisation = new Entity\Parent\Organisation();
            $parentOrganisation->setOrganisation($organisation);
            $parentOrganisation->setParent($parent);
            $parentOrganisation->setContact($contact);
            $this->newEntity($parentOrganisation);

            return $parentOrganisation;
        }

        //We have no parent so create it all
        $parent = new Entity\OParent();
        $parent->setOrganisation($organisation);
        $parent->setContact($contact);
        /** @var Entity\Parent\Type $type */
        $type = $this->findEntityById(Entity\Parent\Type::class, Entity\Parent\Type::TYPE_OTHER);
        $parent->setType($type);

        $this->updateEntity($parent);

        //we have the parent now, but cannot find the organisation, so we create it.
        $parentOrganisation = new Entity\Parent\Organisation();
        $parentOrganisation->setOrganisation($organisation);
        $parentOrganisation->setParent($parent);
        $parentOrganisation->setContact($contact);
        $this->newEntity($parentOrganisation);

        return $parentOrganisation;
    }

    /**
     * @param Entity\OParent $parent
     * @param Entity\Organisation $organisation
     *
     * @return null|Entity\Parent\Organisation
     */
    public function findParentOrganisationInParentByOrganisation(
        Entity\OParent $parent,
        Entity\Organisation $organisation
    ): ?Entity\Parent\Organisation {
        foreach ($parent->getParentOrganisation() as $parentOrganisation) {
            if ($parentOrganisation->getOrganisation() === $organisation) {
                return $parentOrganisation;
            }
        }

        return null;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @return float
     * @throws \Exception
     */
    public function parseTotalFundedByParent(Entity\OParent $parent, Program $program): float
    {
        //Go over each affiliation and sum up what has been paid already
        $totalFunded = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $totalFunded += $this->getVersionService()
                    ->findTotalFundingVersionByAffiliationAndVersion($affiliation, $latestVersion);
            }
        }

        return (float)$totalFunded;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @return float
     * @throws \Exception
     */
    public function parseTotalFundingEuByParent(Entity\OParent $parent, Program $program): float
    {
        //Go over each affiliation and sum up what has been paid already
        $totalFunded = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $totalFunded += $this->getVersionService()
                    ->findTotalFundingEuVersionByAffiliationAndVersion($affiliation, $latestVersion);
            }
        }

        return (float)$totalFunded;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @param int $year
     * @return float
     */
    public function _parseContributionPaid(Entity\OParent $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionPaid = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            $contributionPaid += $this->getAffiliationService()->parseContributionPaid($affiliation, $year);
        }

        return (float)$contributionPaid;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @param int $year
     * @return float
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function _parseContributionDue(Entity\OParent $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionDue = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $contributionDue += $this->getAffiliationService()
                    ->parseContributionDue($affiliation, $latestVersion, $year);
            }
        }

        return (float)$contributionDue;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @param int $year
     * @return float
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function parseContribution(Entity\OParent $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contribution = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $contribution += $this->getAffiliationService()
                    ->parseContribution($affiliation, $latestVersion, null, $year);
            }
        }

        return (float)$contribution;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @param int $year
     * @return float
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function parseBalance(Entity\OParent $parent, Program $program, int $year): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionBalance = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject());

            if (null !== $latestVersion) {
                $contributionBalance += $this->getAffiliationService()
                    ->parseBalance($affiliation, $latestVersion, $year);
            }
        }

        return (float)$contributionBalance;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @return float
     * @throws \Exception
     */
    public function parseTotalExtraVariableBalanceByParent(Entity\OParent $parent, Program $program): float
    {
        //Go over each affiliation and sum up what has been paid already
        $balanceTotal = 0;
        foreach ($this->getProjectService()->findProjectsByParent($parent, $program) as $project) {
            $version = $this->getProjectService()->getLatestProjectVersion($project);

            //Only add the balance when there is a version
            if (null !== $version) {
                $balanceTotal += $this->parseExtraVariableBalanceByParentAndVersion($parent, $version);
            }
        }

        return round($balanceTotal);
    }

    /**
     * @param Entity\OParent $parent
     * @param Version $version
     *
     * @return float
     */
    public function parseExtraVariableBalanceByParentAndVersion(Entity\OParent $parent, Version $version): float
    {
        /**
         * The formula is
         *
         * 1.5% * SUM FREE RIDERS * FUNDING BY C CHAMBER / 3 * MEMBERSHIPS * SUM OF FUNDING OF ALL C CHAMBERS
         *
         */
        $sumOfFreeRiders = $this->getVersionService()->findTotalFundingVersionByFreeRidersInVersion($version);
        $sumOfFundingByCChamber = $this->getVersionService()
            ->findTotalFundingVersionByParentAndVersion($parent, $version);
        $sumOfFundingByCChambers = $this->getVersionService()->findTotalFundingVersionByCChambersInVersion($version);
        $amountOfMemberships = $this->parseMembershipFactor($parent);

        if ($amountOfMemberships === 0 || $sumOfFundingByCChambers < 0.001) {
            return (float)0;
        }

        return (0.015 * $sumOfFreeRiders * $sumOfFundingByCChamber) / (3 * $amountOfMemberships
                * $sumOfFundingByCChambers);
    }

    /**
     * @param Entity\OParent $parent
     * @return int
     */
    public function parseMembershipFactor(Entity\OParent $parent): int
    {
        return \count($this->parseMemberships($parent));
    }

    /**
     * @param Entity\OParent $parent
     * @return array
     */
    public function parseMemberships(Entity\OParent $parent): array
    {
        $memberships = [];

        if ($parent->getMemberType() === Entity\OParent::MEMBER_TYPE_MEMBER) {
            $memberships[] = 'AENEAS';
        }
        if ($parent->getArtemisiaMemberType() === Entity\OParent::ARTEMISIA_MEMBER_TYPE_MEMBER) {
            $memberships[] = 'ARTEMIS-IA';
        }
        if ($parent->getEpossMemberType() === Entity\OParent::EPOSS_MEMBER_TYPE_MEMBER) {
            $memberships[] = 'EPOSS';
        }

        return $memberships;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @return int
     */
    public function parseDoaFactor(Entity\OParent $parent, Program $program): int
    {
        return \count($this->parseDoas($parent, $program));
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @return array
     */
    public function parseDoas(Entity\OParent $parent, Program $program): array
    {
        $otherDoa = [];

        foreach ($parent->getDoa() as $doa) {
            if ($doa->getProgram()->getId() === $program->getId()) {
                $otherDoa[] = $program->getProgram();
            }
        }

        if ($parent->getArtemisiaMemberType() === Entity\OParent::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER) {
            $otherDoa[] = 'ARTEMIS-IA';
        }
        if ($parent->getEpossMemberType() === Entity\OParent::ARTEMISIA_MEMBER_TYPE_DOA_SIGNER) {
            $otherDoa[] = 'EPOSS';
        }

        return $otherDoa;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @param int $year
     * @return array
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function renderProjectsByParentInYear(Entity\OParent $parent, Program $program, int $year): array
    {
        //Sort the projects per call
        $projects = [];
        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            /** @var Call $call */
            $call = $affiliation->getProject()->getCall();

            //Initialize the array
            if (!array_key_exists($call->getId(), $projects)) {
                $projects[$call->getId()]['affiliation'] = [];
                $projects[$call->getId()]['call'] = $call;
                $projects[$call->getId()]['totalFunding'] = 0;
                $projects[$call->getId()]['totalContribution'] = 0;
            }

            $latestVersion = $this->getProjectService()->getLatestProjectVersion($affiliation->getProject());

            //Skip the rest of the calculation if a project has no version
            if (null === $latestVersion) {
                continue;
            }

            $funding = $this->getVersionService()->findTotalFundingVersionByAffiliationAndVersion(
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
                'affiliation' => $affiliation,
                'funding' => $funding,
                'contribution' => $contribution
            ];


            $projects[$call->getId()]['totalFunding'] += $funding;
            $projects[$call->getId()]['totalContribution'] += $contribution;
        }

        return $projects;
    }

    /**
     * @param Entity\OParent $parent
     * @param Version $version
     * @return bool
     */
    public function hasExtraVariableBalanceByParentAndVersion(Entity\OParent $parent, Version $version): bool
    {
        return $this->parseExtraVariableBalanceByParentAndVersion($parent, $version) > 0;
    }

    /**
     * @param Entity\OParent $parent
     * @return float
     */
    public function parseInvoiceFactor(Entity\OParent $parent): float
    {
        if ($parent->isMember()) {
            return 1.5;
        }

        return 2.5;
    }

    /**
     * @param Entity\OParent $parent
     * @param Program $program
     * @param int $year
     * @param array|null $includeAffiliations
     * @return float
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function parseTotal(
        Entity\OParent $parent,
        Program $program,
        int $year,
        array $includeAffiliations = null
    ): float {
        //Go over each affiliation and sum up what has been paid already
        $contributionTotal = 0;

        foreach ($this->getAffiliationService()->findAffiliationByParentAndProgramAndWhich(
            $parent,
            $program
        ) as $affiliation) {
            //Skip the affiliations which are not in the $include affiliations table
            if (null !== $includeAffiliations && !\in_array($affiliation, $includeAffiliations, true)) {
                continue;
            }

            $latestVersion = $this->getProjectService()->getLatestProjectVersion($affiliation->getProject());

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

    /**
     * @return ArrayCollection|Entity\OParent[]
     */
    public function findActiveParents(): ArrayCollection
    {
        /** @var Repository\OParent $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\OParent::class);

        return new ArrayCollection($repository->findActiveParents());
    }

    /**
     * @param string $name
     * @return ArrayCollection|Entity\Parent\Organisation[]
     */
    public function findParentOrganisationByNameLike(string $name)
    {
        /** @var Repository\Parent\Organisation $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Parent\Organisation::class);

        return new ArrayCollection($repository->findParentOrganisationByNameLike($name));
    }

    /**
     * @param Program $program
     * @return ArrayCollection
     */
    public function findParentsForInvoicing(Program $program): ArrayCollection
    {
        /** @var Repository\OParent $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\OParent::class);

        return new ArrayCollection($repository->findParentsForInvoicing($program));
    }

    /**
     * @return ArrayCollection
     */
    public function findParentsForExtraInvoicing(): ArrayCollection
    {
        /** @var Repository\OParent $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\OParent::class);

        return new ArrayCollection($repository->findParentsForExtraInvoicing());
    }

    /**
     * @param Entity\OParent $parent
     *
     * @return Contact
     */
    public function getFinancialContact(Entity\OParent $parent): ?Contact
    {
        if ($parent->getFinancial()->count() === 1) {
            return $parent->getFinancial()->first()->getContact();
        }

        return null;
    }

    /**
     * @param Entity\OParent $parent
     * @param int $year
     *
     * @return Entity\Parent\Invoice[]|Collection|iterable
     */
    public function findParentInvoiceByParentYear(Entity\OParent $parent, int $year): iterable
    {
        return $parent->getInvoice()->filter(
            function (Entity\Parent\Invoice $invoice) use ($year) {
                return $invoice->getYear() === $year;
            }
        );
    }

    /**
     * @param Entity\OParent $parent
     * @param int $year
     *
     * @return Entity\Parent\Invoice[]|Collection|iterable
     */
    public function findParentExtraInvoiceByParentYear(
        Entity\OParent $parent,
        int $year
    ): iterable {
        return $parent->getInvoiceExtra()->filter(
            function (Entity\Parent\InvoiceExtra $invoiceExtra) use ($year) {
                return $invoiceExtra->getYear() === $year;
            }
        );
    }

    /**
     * @param Entity\OParent $parent
     * @param int $year
     *
     * @return Entity\Parent\Invoice[]|Collection|iterable
     */
    public function findAllParentExtraInvoiceByParentYear(
        Entity\OParent $parent,
        int $year
    ): iterable {
        return array_merge(
            $this->findParentInvoiceByParentYear($parent, $year)->toArray(),
            $this->findParentExtraInvoiceByParentYear($parent, $year)->toArray()
        );
    }

    /**
     * @param Entity\Parent\Organisation $organisation
     * @return bool
     */
    public function canDeleteParentOrganisation(Entity\Parent\Organisation $organisation): bool
    {
        return $organisation->getAffiliation()->isEmpty();
    }

    /**
     * @param Entity\OParent $parent
     * @param bool $autoGenerate
     * @return ArrayCollection
     */
    public function canCreateInvoice(Entity\OParent $parent, $autoGenerate = false): ArrayCollection
    {
        $errors = [];
        switch (true) {
            case empty($parent->getFinancial()):
                $errors[] = 'No financial organisation (parent financial) set for this parent';
                break;
            case !\is_null($parent->getDateEnd()):
                $errors[] = 'Parent is de-activated';
                break;
            case !$autoGenerate && !empty($parent->getFinancial()) && $parent->getFinancial()->count() !== 1:
                $errors[] = 'More than 1 financial organisation known';
                break;
            default:
                foreach ($parent->getFinancial() as $financial) {
                    if (\is_null($financial->getOrganisation()->getFinancial())) {
                        $errors[] = sprintf('%s has no financial information', $financial->getOrganisation());
                    }

                    if (!\is_null($financial->getOrganisation()->getFinancial()) && empty($financial->getOrganisation()->getFinancial()->getVat())) {
                        $errors[] = sprintf('%s has no VAT number', $financial->getOrganisation());
                    }

                    if (!\is_null($financial->getOrganisation()->getFinancial()) && !empty($financial->getOrganisation()->getFinancial()->getVat()) && $financial->getOrganisation()->getFinancial()->getVatStatus() !== Entity\Financial::VAT_STATUS_VALID) {
                        $errors[] = sprintf('%s has an unvalidated VAT number', $financial->getOrganisation());
                    }
                }
                break;
        }

        return new ArrayCollection($errors);
    }
}
