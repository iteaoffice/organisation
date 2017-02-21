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

namespace Organisation\Options;

/**
 * Interface OrganisationOptionsInterface
 * @package Organisation\Options
 */
interface OrganisationOptionsInterface
{
    /**
     * @return bool
     */
    public function getUseOrganisationMap();

    /**
     * @param $useOrganisationMap
     *
     * @return OrganisationOptionsInterface
     */
    public function setUseOrganisationMap($useOrganisationMap);

    /**
     * @param $countryColor
     *
     * @return ModuleOptions
     */
    public function setCountryColor($countryColor);

    /**
     * @return string
     */
    public function getCountryColor();

    /**
     * Returns the assigned hex color of the country map.
     *
     * @param string $countryColorFaded
     *
     * @return ModuleOptions
     */
    public function setCountryColorFaded($countryColorFaded);

    /**
     * Returns the assigned hex color of the country map.
     *
     * @return string
     */
    public function getCountryColorFaded();

    /**
     * @return string
     */
    public function getOverviewVariableContributionTemplate();

    /**
     * @param string $overviewVariableContributionTemplate
     *
     * @return ModuleOptions
     */
    public function setOverviewVariableContributionTemplate($overviewVariableContributionTemplate);

    /**
     * @return string
     */
    public function getOverviewExtraVariableContributionTemplate();

    /**
     * @param string $overviewExtraVariableContributionTemplate
     *
     * @return ModuleOptions
     */
    public function setOverviewExtraVariableContributionTemplate($overviewExtraVariableContributionTemplate);
}
