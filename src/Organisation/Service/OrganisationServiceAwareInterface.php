<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2014 ITEA Office
 * @license     http://debranova.org/license.txt proprietary
 *
 * @link        http://debranova.org
 */

namespace Organisation\Service;

/**
 * Create a link to an organisation.
 *
 * @category   Organisation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 *
 * @link       http://debranova.org
 */
interface OrganisationServiceAwareInterface
{
    /**
     * The organisation service.
     *
     * @param OrganisationService $organisationService
     */
    public function setOrganisationService(OrganisationService $organisationService);

    /**
     * Get organisation service.
     *
     * @return OrganisationService
     */
    public function getOrganisationService();
}
