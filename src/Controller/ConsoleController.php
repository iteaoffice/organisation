<?php

/**
 * Jield copyright message placeholder.
 *
 * @category  Organisation
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright @copyright Copyright (c) 2004-2017 ITEA Office (http://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Organisation\Service\OrganisationService;

/**
 * Class ConsoleController
 *
 * @package Organisation\Controller
 */
final class ConsoleController extends OrganisationAbstractController
{
    private OrganisationService $organisationService;

    public function __construct(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;
    }
    public function organisationCleanupAction(): void
    {
        $this->organisationService->removeInactiveOrganisations();
    }
}
