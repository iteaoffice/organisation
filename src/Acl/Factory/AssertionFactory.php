<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Acl\Factory;

use Admin\Service\AdminService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Organisation\Acl\Assertion\AssertionAbstract;
use Organisation\Service\OrganisationService;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AssertionFactory
 *
 * @package Organisation\Acl\Factory
 */
class AssertionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|ServiceLocatorInterface $container
     * @param string                                     $requestedName
     * @param array|null                                 $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $assertion AssertionAbstract */
        $assertion = new $requestedName();

        $assertion->setServiceLocator($container);

        /** @var AdminService $adminService */
        $adminService = $container->get(AdminService::class);
        $assertion->setAdminService($adminService);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $assertion->setContactService($contactService);

        //Inject the logged in user if applicable
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get('Application\Authentication\Service');
        if ($authenticationService->hasIdentity()) {
            $assertion->setContact($authenticationService->getIdentity());
        }

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $assertion->setOrganisationService($organisationService);

        return $assertion;
    }

    /**
     * @param ServiceLocatorInterface $container
     * @param null                    $canonicalName
     * @param                         $requestedName
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $container, $canonicalName = null, $requestedName = null)
    {
        return $this($container, $requestedName);
    }
}
