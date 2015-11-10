<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2015 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://itea3.org
 */

namespace Organisation\Service;

/**
 * Create a link to an organisation.
 *
 * @category   Organisation
 *
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    https://itea3.org/licence.txt proprietary
 *
 * @link       https://itea3.org
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
