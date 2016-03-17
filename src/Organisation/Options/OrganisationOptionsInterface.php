<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Organisation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
 */
namespace Organisation\Options;

/**
 * ITEA Office copyright message placeholder.
 *
 * @category   Organisation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright  2004-2015 ITEA Office
 * @license    https://itea3.org/license.txt proprietary
 *
 * @link       https://itea3.org
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
}
