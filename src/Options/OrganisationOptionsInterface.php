<?php

/**
 * ITEA Office all rights reserved
 *
 * @category   Organisation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */

declare(strict_types=1);

namespace Organisation\Options;

/**
 * Interface OrganisationOptionsInterface
 *
 * @package Organisation\Options
 */
interface OrganisationOptionsInterface
{
    public function getOverviewVariableContributionTemplate(): string;

    public function setOverviewVariableContributionTemplate(
        string $overviewVariableContributionTemplate
    ): ModuleOptions;

    public function getOverviewExtraVariableContributionTemplate(): string;

    public function setOverviewExtraVariableContributionTemplate(
        string $overviewExtraVariableContributionTemplate
    ): ModuleOptions;
}
