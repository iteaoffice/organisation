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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Service;

use Contact\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Organisation\Entity;
use Organisation\Repository;
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
    public function findParentById($id)
    {
        return $this->findEntityById(Entity\OParent::class, $id);
    }

    /**
     * @param string $name
     *
     * @return null|Entity\Parent\Status|object
     */
    public function findParentStatusByName(string $name)
    {
        return $this->getEntityManager()->getRepository(Entity\Parent\Status::class)->findOneBy(['status' => $name]);
    }

    /**
     * @param string $name
     *
     * @return null|Entity\Parent\Type|object
     */
    public function findParentTypeByName(string $name)
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
        if (!is_null($organisation->getParent())) {
            $parentOrganisation = $this->findParentOrganisationInParentByOrganisation(
                $organisation->getParent(),
                $organisation
            );

            if (!is_null($parentOrganisation)) {
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
        if (!is_null($organisation->getParentOrganisation())) {
            $parent = $organisation->getParentOrganisation()->getParent();

            $parentOrganisation = $this->findParentOrganisationInParentByOrganisation($parent, $organisation);

            if (!is_null($parentOrganisation)) {
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
        /** @var Entity\Parent\Status $status */
        $status = $this->findEntityById(Entity\Parent\Status::class, Entity\Parent\Status::STATUS_FREE_RIDER);
        $parent->setStatus($status);

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
     * @return null|\Organisation\Entity\Parent\Organisation
     */
    public function findParentOrganisationInParentByOrganisation(
        Entity\OParent $parent,
        Entity\Organisation $organisation
    ) {
        foreach ($parent->getParentOrganisation() as $parentOrganisation) {
            if ($parentOrganisation->getOrganisation() === $organisation) {
                return $parentOrganisation;
            }
        }

        return null;
    }

    /**
     * @param $parent
     *
     * @return float|int
     * @throws \Exception
     */
    public function parseTotalFundedByParent($parent)
    {
        //Go over each affiliation and sum up what has been paid already
        $totalFunded = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject(), null, null, false, false);

            if (!is_null($latestVersion)) {
                $totalFunded += $this->getVersionService()
                    ->findTotalFundingVersionByAffiliationAndVersion($affiliation, $latestVersion);
            }
        }

        return $totalFunded;
    }

    /**
     * @param $parent
     *
     * @return float|int
     * @throws \Exception
     */
    public function parseTotalFundingEuByParent($parent)
    {
        //Go over each affiliation and sum up what has been paid already
        $totalFunded = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject(), null, null, false, false);

            if (!is_null($latestVersion)) {
                $totalFunded += $this->getVersionService()
                    ->findTotalFundingEuVersionByAffiliationAndVersion($affiliation, $latestVersion);
            }
        }

        return $totalFunded;
    }

    /**
     * Calculate the amount of contribution paid by the parent
     *
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return float;
     */
    public function parseContributionPaid(Entity\OParent $parent, int $year, int $period)
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionPaid = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $contributionPaid += $this->getAffiliationService()->parseContributionPaid($affiliation, $year, $period);
        }

        return $contributionPaid;
    }

    /**
     * Calculate the amount of contribution due by the parent
     *
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return float;
     */
    public function parseContributionDue(Entity\OParent $parent, int $year, int $period): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionDue = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject(), null, null, false, false);

            if (!is_null($latestVersion)) {
                $contributionDue += $this->getAffiliationService()
                    ->parseContributionDue($affiliation, $latestVersion, $year, $period);
            }
        }

        return $contributionDue;
    }

    /**
     * Calculate the amount of contribution due by the parent
     *
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return float;
     */
    public function parseContribution(Entity\OParent $parent, int $year, int $period): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contribution = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject(), null, null, false, false);

            if (!is_null($latestVersion)) {
                $contribution += $this->getAffiliationService()
                    ->parseContribution($affiliation, $latestVersion, $year, $period);
            }
        }

        return $contribution;
    }

    /**
     * Calculate the amount of contribution due by the parent
     *
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return float;
     */
    public function parseBalance(Entity\OParent $parent, int $year, int $period): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionBalance = 0;
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject(), null, null, false, false);

            if (!is_null($latestVersion)) {
                $contributionBalance += $this->getAffiliationService()
                    ->parseBalance($affiliation, $latestVersion, $year, $period);
            }
        }

        return $contributionBalance;
    }

    /**
     * @param $parent
     *
     * @return float
     * @throws \Exception
     */
    public function parseTotalExtraVariableBalanceByParent(Entity\OParent $parent): float
    {
        //Go over each affiliation and sum up what has been paid already
        $balanceTotal = 0;
        foreach ($this->getProjectService()->findProjectsByParent($parent) as $project) {
            $version = $this->getProjectService()->getLatestProjectVersion($project, null, null, false, false);

            //Only add the balance when there is a version
            if (!is_null($version)) {
                $balanceTotal += $this->parseExtraVariableBalanceByParentAndVersion($parent, $version);
            }
        }

        return $balanceTotal;
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

        return (float)(0.015 * $sumOfFreeRiders * $sumOfFundingByCChamber) / (3 * $amountOfMemberships
                * $sumOfFundingByCChambers);
    }

    /**
     * @param Entity\OParent $parent
     * @return int
     */
    public function parseMembershipFactor(Entity\OParent $parent): int
    {
        $amountOfMemberships = 1;

        if ($parent->getArtemisiaMemberType() !== Entity\OParent::ARTEMISIA_MEMBER_TYPE_NO_MEMBER) {
            $amountOfMemberships++;
        }
        if ($parent->getEpossMemberType() !== Entity\OParent::EPOSS_MEMBER_TYPE_NO_MEMBER) {
            $amountOfMemberships++;
        }

        return $amountOfMemberships;
    }

    /**
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     * @return array
     */
    public function renderProjectsByParentInYearAndPeriod(Entity\OParent $parent, int $year, int $period): array
    {
        //Sort the projects per call
        $projects = [];
        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $call = $affiliation->getProject()->getCall();
            //Initialize the array
            if (!array_key_exists($call->getId(), $projects)) {
                $projects[$call->getId()]['affiliation'] = [];
                $projects[$call->getId()]['call'] = $call;
                $projects[$call->getId()]['totalFunding'] = 0;
                $projects[$call->getId()]['totalContribution'] = 0;
            }

            $latestVersion = $this->getProjectService()->getLatestProjectVersion(
                $affiliation->getProject(),
                null,
                null,
                false,
                false
            );

            //Skip the rest of the calculation if a project has no version
            if (is_null($latestVersion)) {
                continue;
            }

            $funding = $this->getVersionService()->findTotalFundingVersionByAffiliationAndVersion(
                $affiliation,
                $latestVersion
            );
            $contribution = $this->getAffiliationService()->parseContribution(
                $affiliation,
                $latestVersion,
                $year,
                $period
            );

            $projects[$call->getId()]['affiliation'][] = [
                'affiliation'  => $affiliation,
                'funding'      => $funding,
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
     * @param int $year
     *
     * @return float
     */
    public function parseInvoiceFactor(Entity\OParent $parent, int $year): float
    {
        $fee = $this->getProjectService()->findProjectFeeByParentAndYear($parent, $year);

        if (is_null($fee)) {
            return 0;
        }

        return (float)$fee->getPercentage();
    }

    /**
     * Calculate the amount of contribution due by the parent
     *
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return float;
     */
    public function parseTotal(Entity\OParent $parent, int $year, int $period): float
    {
        //Go over each affiliation and sum up what has been paid already
        $contributionTotal = 0;

        foreach ($this->getAffiliationService()->findAffiliationByParentAndWhich($parent) as $affiliation) {
            $latestVersion = $this->getProjectService()
                ->getLatestProjectVersion($affiliation->getProject(), null, null, false, false);

            if (!is_null($latestVersion)) {
                $contributionTotal += $this->getAffiliationService()->parseTotal(
                    $affiliation,
                    $latestVersion,
                    $year,
                    $period
                );
            }
        }

        return $contributionTotal;
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
     * @return ArrayCollection
     */
    public function findParentsForInvoicing(): ArrayCollection
    {
        /** @var Repository\OParent $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\OParent::class);

        return new ArrayCollection($repository->findParentsForInvoicing());
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
     * @param int $period
     *
     * @return Entity\Parent\Invoice[]|Collection|iterable
     */
    public function findParentInvoiceByParentYearAndPeriod(Entity\OParent $parent, int $year, int $period): iterable
    {
        //Cast to int as some values can originate form templates (== twig > might be string)
        $year = (int)$year;
        $period = (int)$period;

        return $parent->getInvoice()->filter(
            function (Entity\Parent\Invoice $invoice) use ($period, $year) {
                return $invoice->getPeriod() === $period && $invoice->getYear() === $year;
            }
        );
    }

    /**
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return Entity\Parent\Invoice[]|Collection|iterable
     */
    public function findParentExtraInvoiceByParentYearAndPeriod(
        Entity\OParent $parent,
        int $year,
        int $period
    ): iterable {
        //Cast to int as some values can originate form templates (== twig > might be string)
        $year = (int)$year;
        $period = (int)$period;

        return $parent->getInvoiceExtra()->filter(
            function (Entity\Parent\InvoiceExtra $invoiceExtra) use ($period, $year) {
                return $invoiceExtra->getPeriod() === $period && $invoiceExtra->getYear() === $year;
            }
        );
    }

    /**
     * @param Entity\OParent $parent
     *
     * @return ArrayCollection
     */
    public function canCreateInvoice(Entity\OParent $parent): ArrayCollection
    {
        $errors = [];
        switch (true) {
            case empty($parent->getFinancial()):
                $errors[] = 'No financial organisation (parent financial) set for this parent';
                break;
            case !is_null($parent->getDateEnd()):
                $errors[] = 'Parent is de-activated';
                break;
            case !empty($parent->getFinancial()) && $parent->getFinancial()->count() !== 1:
                $errors[] = 'More than 1 financial organisation known';
                break;
        }

        return new ArrayCollection($errors);
    }
}
