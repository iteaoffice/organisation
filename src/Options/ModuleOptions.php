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

use Zend\Stdlib\AbstractOptions;

/**
 * Create a link to an project.
 *
 * @category   Organisation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
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
     * @return string
     */
    public function getCountryColor()
    {
        return $this->countryColor;
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
     * Returns the assigned hex color of the country map.
     *
     * @return string
     */
    public function getCountryColorFaded()
    {
        return $this->countryColorFaded;
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
}
