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

use Zend\Stdlib\AbstractOptions;

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
