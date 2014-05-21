<?php
/**
 * ITEA Office copyright message placeholder
 *
 * PHP Version 5
 *
 * @category    Project
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2014 ITEA Office
 * @license     http://debranova.org/license.txt proprietary
 * @link        http://debranova.org
 */
namespace Organisation\Service;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create a link to an project
 *
 * @category   Project
 * @package    Service
 * @author     Johan van der Heide <johan.van.der.heide@itea3.org>
 * @license    http://debranova.org/licence.txt proprietary
 * @link       http://debranova.org
 */
class ServiceInitializer implements InitializerInterface
{
    /**
     * @param                         $instance
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ServiceAbstract
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof OrganisationServiceAwareInterface) {
            $organisationService = $serviceLocator->get('organisation_organisation_service');
            $instance->setOrganisationService($organisationService);
        }
    }
}
