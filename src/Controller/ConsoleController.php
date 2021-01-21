<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Organisation\Service\OrganisationService;

/**
 * Class ConsoleController
 *
 * @package Organisation\Controller
 */
final class ConsoleController extends AbstractController
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
