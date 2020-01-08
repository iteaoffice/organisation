<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Organisation\Entity\Parent\Organisation;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class MergeOrganisation
 *
 * @package Organisation\Controller\Plugin
 */
final class MergeParentOrganisation extends AbstractPlugin
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
            \error_log($e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage());
        }

        return $response;
    }
}
