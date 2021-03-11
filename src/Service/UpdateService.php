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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use General\Service\EmailService;
use Organisation\Entity\Description;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Entity\Web;

/**
 * Class UpdateService
 *
 * @package Organisation\Service
 */
class UpdateService extends AbstractService
{
    private OrganisationService $organisationService;
    private EmailService $emailService;

    public function __construct(
        EntityManager $entityManager,
        OrganisationService $organisationService,
        EmailService $emailService
    ) {
        parent::__construct($entityManager);
        $this->organisationService = $organisationService;
        $this->emailService        = $emailService;
    }

    public function findPendingUpdates(): array
    {
        return $this->entityManager->getRepository(Update::class)->findBy(
            ['dateApproved' => null],
            ['dateCreated' => Criteria::ASC]
        );
    }

    public function countPendingUpdates(): int
    {
        return $this->entityManager->getRepository(Update::class)->count(['dateApproved' => null]);
    }

    public function hasPendingUpdates(Organisation $organisation): bool
    {
        return ($this->entityManager->getRepository(Update::class)->count(
            [
                    'dateApproved' => null,
                    'organisation' => $organisation
                ]
        ) > 0);
    }

    public function approveUpdate(Update $update): bool
    {
        /** @var Organisation $organisation */
        $organisation = $update->getOrganisation();
        $description  = $organisation->getDescription();
        if ($organisation->getDescription() === null) {
            $description = new Description();
            $description->setOrganisation($organisation);
            $this->entityManager->persist($description);
        }
        $description->setDescription($update->getDescription());
        $organisation->setDescription($description);
        $organisation->setType($update->getType());

        // Set logo when present
        if ($update->getLogo() !== null) {
            $logo = $organisation->getLogo()->first();
            if (! $logo) {
                $logo = new Logo();
                $logo->setOrganisation($organisation);
                $this->entityManager->persist($logo);
            }
            $logo->setContentType($update->getLogo()->getContentType());
            $logo->setLogoExtension($update->getLogo()->getLogoExtension());
            $logo->setOrganisationLogo($update->getLogo()->getOrganisationLogo());
            $organisation->setLogo(new ArrayCollection([$logo]));
        }

        //Handle the web :)
        //When an request for update comes in we assume that the URL povided by the dude is aways the only MAIN website we need
        //So therefore we make all others NON main and promote the one given as the new MAIN

        //First invalidate all the mains
        foreach ($update->getOrganisation()->getWeb() as $web) {
            $web->setMain(Web::NOT_MAIN);
            $this->entityManager->persist($web);
        }

        //We first try to find the web
        if (null !== $update->getWebsite()) {
            $web = $this->organisationService->findWebByWeb($update->getOrganisation(), $update->getWebsite());

            if (null === $web) {
                $web = new Web();
                $web->setWeb($update->getWebsite());
                $web->setOrganisation($update->getOrganisation());
            }

            $web->setMain(Web::MAIN);
            $this->entityManager->persist($web);
        }

        $update->setDateApproved(new DateTime());

        $this->entityManager->flush();

        //Send the email
        $email = $this->emailService->createNewWebInfoEmailBuilder('/organisation/update/approved');
        $email->addContactTo($update->getContact());
        $email->setTemplateVariable('display_name', $update->getContact()->getDisplayName());
        $this->emailService->sendBuilder($email);


        return true;
    }
}
