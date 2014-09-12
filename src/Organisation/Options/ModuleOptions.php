<?php
/**
 * ARTEMIS-IA Office copyright message placeholder
 *
 * @category   Organisation
 * @package    Options
 * @author     Andre Hebben <andre.hebben@artemis-ia.eu>
 * @copyright  2004-2014 ARTEMIS-IA Office
 * @license    http://debranova.org/license.txt proprietary
 * @link       http://debranova.org
 */
namespace Organisation\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Create a link to an project
 *
 * @category   Organisation
 * @package    Options
 * @author     Andre Hebben <andre.hebben@artemis-ia.eu>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class ModuleOptions extends AbstractOptions implements OrganisationOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * Use Organisation Map
     * @var bool
     */
    protected $useOrganisationMap = true;

    /**
     * @return bool
     */
    public function getUseOrganisationMap(){
        return $this->useOrganisationMap;
    }

    /**
     * @param $useOrganisationMap
     * @return OrganisationOptionsInterface
     */
    public function setUseOrganisationMap($useOrganisationMap){
        $this->useOrganisationMap =  $useOrganisationMap;
        return $this;
    }


}
