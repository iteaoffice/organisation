<?php

/**
 * ARTEMIS-IA Office copyright message placeholder.
 *
 * @category   Organisation
 *
 * @author     Andre Hebben <andre.hebben@artemis-ia.eu>
 * @copyright  2004-2014 ARTEMIS-IA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
 */
namespace Organisation\Options;

/**
 * ARTEMIS-IA Office copyright message placeholder.
 *
 * @category   Organisation
 *
 * @author     Andre Hebben <andre.hebben@artemis-ia.eu>
 * @copyright  2004-2014 ARTEMIS-IA Office
 * @license    http://debranova.org/license.txt proprietary
 *
 * @link       http://debranova.org
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
