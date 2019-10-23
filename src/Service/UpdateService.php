<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2019 ITEA Office (https://itea3.org)
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
use Organisation\Entity\Update;

/**
 * Class UpdateService
 * @package Organisation\Service
 */
class UpdateService extends AbstractService
{
    /**
     * @var EmailService
     */
    private $emailService;

    public function __construct(
        EntityManager $entityManager,
        EmailService  $emailService
    )
    {
        parent::__construct($entityManager);
        $this->emailService = $emailService;
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

    public function approveUpdate(Update $update): bool
    {
        $organisation = $update->getOrganisation();
        $description  = $organisation->getDescription();
        if ($organisation->getDescription() === null) {
            $description = new Description();
            $description->setOrganisation($organisation);
        }
        $description->setDescription($update->getDescription());
        $organisation->setDescription($description);
        $organisation->setType($update->getType());

        // Set logo when present
        if ($update->getLogo() !== null) {
            $logo = $organisation->getLogo()->first();
            if (!$logo) {
                $logo = new Logo();
                $logo->setOrganisation($organisation);
            }
            $logo->setContentType($update->getLogo()->getContentType());
            $logo->setLogoExtension($update->getLogo()->getLogoExtension());
            $logo->setOrganisationLogo($update->getLogo()->getOrganisationLogo());
            $organisation->setLogo(new ArrayCollection([$logo]));
        }

        $this->save($organisation);

        $update->setDateApproved(new DateTime());
        $this->save($update);

        // Send confirmation mail
        $this->emailService->setWebInfo('/organisation/update/approved');
        $this->emailService->addToEmailAddress($update->getContact()->getEmail());
        $this->emailService->setTemplateVariable('display_name', $update->getContact()->getDisplayName());

        return $this->emailService->send();
    }
}
