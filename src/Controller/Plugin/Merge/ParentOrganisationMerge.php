<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin\Merge;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use ErrorHeroModule\Handler\Logging;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Organisation\Entity\Parent\Organisation;

/**
 * Class ParentOrganisation
 * @package Organisation\Controller\Plugin\Merge
 */
final class ParentOrganisationMerge extends AbstractPlugin
{
    private EntityManager $entityManager;
    private Logging $errorLogger;

    public function __construct(EntityManager $entityManager, Logging $errorLogger)
    {
        $this->entityManager = $entityManager;
        $this->errorLogger   = $errorLogger;
    }

    public function __invoke(Organisation $mainOrganisation, Organisation $otherOrganisation): array
    {
        $response = ['success' => true, 'errorMessage' => ''];

        try {
            // Step 1: Move the projects
            foreach ($otherOrganisation->getAffiliation() as $key => $affiliation) {
                $affiliation->setParentOrganisation($mainOrganisation);

                $this->entityManager->persist($affiliation);
            }
            // Step 12: Persist main affiliation, remove the other + flush and update permissions
            $this->entityManager->remove($otherOrganisation);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            $response = ['success' => false, 'errorMessage' => $e->getMessage()];
            if ($this->errorLogger instanceof Logging) {
                $this->errorLogger->handleErrorException($e, new Request());
            }
        }

        return $response;
    }
}
