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
            if (!is_null(
                $parentOrganisation = $this->findParentOrganisationInParentByOrganisation(
                    $organisation->getParent(),
                    $organisation
                )
            )
            ) {
                return $parentOrganisation;
            } else {
                //we have the parent now, but cannot find the organisation, so we create it.
                $parentOrganisation = new Entity\Parent\Organisation();
                $parentOrganisation->setOrganisation($organisation);
                $parentOrganisation->setParent($organisation->getParent());
                $parentOrganisation->setContact($contact);
                $this->newEntity($parentOrganisation);
            }
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

            $balanceTotal += $this->parseExtraVariableBalanceByParentAndVersion($parent, $version);
        }

        return $balanceTotal;
    }

    /**
     * @param Entity\OParent $parent
     * @param Version $version
     *
     * @return float
     */
    public function parseExtraVariableBalanceByParentAndVersion(Entity\OParent $parent, Version $version)
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
        $amountOfMemberships = count($this->parseInvoiceFactor($parent, date("Y"))->member);

        if ($amountOfMemberships === 0 || $sumOfFundingByCChambers < 0.001) {
            return (float)0;
        }

        return (float)(0.015 * $sumOfFreeRiders * $sumOfFundingByCChamber) / (3 * $amountOfMemberships
                * $sumOfFundingByCChambers);
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
     * @param Entity\OParent $parent
     * @return int
     */
    public function parseMembershipFactor(Entity\OParent $parent): int
    {
        $amountOfMemberships = 1;

        if ($parent->getArtemisiaMemberType() === Entity\OParent::ARTEMISIA_MEMBER_TYPE_MEMBER) {
            $amountOfMemberships++;
        }
        if ($parent->getEpossMemberType() === Entity\OParent::EPOSS_MEMBER_TYPE_MEMBER) {
            $amountOfMemberships++;
        }

        return $amountOfMemberships;
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
    public function getFinancialContact(Entity\OParent $parent)
    {
        if (!is_null($parent->getFinancial())) {
            return $parent->getFinancial()->getContact();
        } else {
            return null;
        }
    }

    /**
     * @param Entity\OParent $parent
     * @param int $year
     * @param int $period
     *
     * @return Entity\Parent\Invoice[]|Collection
     */
    public function findParentInvoiceByParentYearAndPeriod(Entity\OParent $parent, int $year, int $period)
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
     *
     * @return array
     */
    public function canCreateInvoice(Entity\OParent $parent)
    {
        $errors = [];
        switch (true) {
            case is_null($parent->getFinancial()):
                $errors[] = 'No financial organisation (parent financial) set for this parent';
                break;
            case !is_null($parent->getDateEnd()):
                $errors[] = 'Parent is de-activated';
                break;
            case is_null($parent->getFinancial()->getOrganisation()->getFinancial()):
                $errors[] = 'No financial information set for this organisation';
                break;
            case is_null($parent->getFinancial()->getContact()):
                $errors[] = 'No financial contact set for this organisation';
                break;
        }

        return $errors;
    }
}
