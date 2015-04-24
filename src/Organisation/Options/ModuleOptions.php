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

use Zend\Stdlib\AbstractOptions;

/**
 * Create a link to an project.
 *
 * @category   Organisation
 *
 * @author     Andre Hebben <andre.hebben@artemis-ia.eu>
 * @license    http://debranova.org/licence.txt proprietary
 *
 * @link       http://debranova.org
 */
class ModuleOptions extends AbstractOptions implements OrganisationOptionsInterface
{
    /**
     * Turn off strict options mode.
     */
    protected $__strictMode__ = false;

    /**
     * Use Organisation Map.
     *
     * @var bool
     */
    protected $useOrganisationMap = true;
    
    /**
     * Color to use on country map.
     *
     * @var string
     */
    protected $countryColor = '#00a651';
    
    /**
     * Color to use on country map for faded countries.
     *
     * @var string
     */
    protected $countryColorFaded = '#005C00';

    /**
     * @return bool
     */
    public function getUseOrganisationMap()
    {
        return $this->useOrganisationMap;
    }

    /**
     * @param $useOrganisationMap
     *
     * @return OrganisationOptionsInterface
     */
    public function setUseOrganisationMap($useOrganisationMap)
    {
        $this->useOrganisationMap = $useOrganisationMap;

        return $this;
    }
    
    /**
     * @param $countryColor
     *
     * @return ModuleOptions
     */
    public function setCountryColor($countryColor)
    {
        $this->countryColor = $countryColor;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryColor()
    {
        return $this->countryColor;
    }

    /**
     * Returns the assigned hex color of the country map.
     *
     * @param string $countryColorFaded
     *
     * @return ModuleOptions
     */
    public function setCountryColorFaded($countryColorFaded)
    {
        $this->countryColorFaded = $countryColorFaded;

        return $this;
    }

    /**
     * Returns the assigned hex color of the country map.
     *
     * @return string
     */
    public function getCountryColorFaded()
    {
        return $this->countryColorFaded;
    }
}
