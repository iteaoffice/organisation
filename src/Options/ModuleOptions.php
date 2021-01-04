<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * @package Organisation\Options
 */
class ModuleOptions extends AbstractOptions implements OrganisationOptionsInterface
{
    protected $overviewVariableContributionTemplate = '';

    protected $overviewExtraVariableContributionTemplate = '';

    public function getOverviewVariableContributionTemplate(): string
    {
        return $this->overviewVariableContributionTemplate;
    }

    public function setOverviewVariableContributionTemplate(string $overviewVariableContributionTemplate): self
    {
        $this->overviewVariableContributionTemplate = $overviewVariableContributionTemplate;

        return $this;
    }

    public function getOverviewExtraVariableContributionTemplate(): string
    {
        return $this->overviewExtraVariableContributionTemplate;
    }

    public function setOverviewExtraVariableContributionTemplate(
        string $overviewExtraVariableContributionTemplate
    ): self {
        $this->overviewExtraVariableContributionTemplate = $overviewExtraVariableContributionTemplate;

        return $this;
    }
}
