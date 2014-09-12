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
namespace Program\Options;

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
interface OrganisationOptionsInterface
{

    /**
     * @return bool
     */
    public function getUseOrganisationMap();

    /**
     * @param $useOrganisationMap
     * @return OrganisationOptionsInterface
     */
    public function setUseOrganisationMap($useOrganisationMap);

}
