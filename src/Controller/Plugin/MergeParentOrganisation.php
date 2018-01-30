<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Organisation\Entity\Parent\Organisation;

/**
 * Class MergeOrganisation
 *
 * @package Organisation\Controller\Plugin
 */
class MergeParentOrganisation extends AbstractOrganisationPlugin
{
    /**
     * @var array
     */
    protected $debug = [];
    /**
     * @var EntityManager
     */
    protected $entityManager;


    /**
     * MergeOrganisation magic invokable
     *
     * @param Organisation $mainOrganisation
     * @param Organisation $otherOrganisation
     * @param int          $costAndEffortStrategy
     *
     * @return array
     */
    public function __invoke(
        Organisation $mainOrganisation,
        Organisation $otherOrganisation
    ): array {
        $response = ['success' => true, 'errorMessage' => ''];

        try {
            // Step 1: Move the projects
            foreach ($otherOrganisation->getAffiliation() as $key => $affiliation) {
                $affiliation->setParentOrganisation($mainOrganisation);

                $this->getEntityManager()->persist($affiliation);
            }

            // Step 12: Persist main affiliation, remove the other + flush and update permissions

            $this->getEntityManager()->remove($otherOrganisation);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            $response = ['success' => false, 'errorMessage' => $e->getMessage()];
            error_log($e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage());
        }

        return $response;
    }
}
