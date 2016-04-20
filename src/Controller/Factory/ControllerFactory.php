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
namespace Organisation\Controller\Factory;

use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Invoice\Service\InvoiceService;
use Organisation\Controller\OrganisationAbstractController;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ControllerFactory
 *
 * @package Project\Controller\Factory
 */
class ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|ControllerManager $container
     * @param                                      $requestedName
     * @param array|null                           $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var OrganisationAbstractController $controller */
        $controller = new $requestedName();

        $serviceManager = $container->getServiceLocator();

        /** @var FormService $formService */
        $formService = $serviceManager->get(FormService::class);
        $controller->setFormService($formService);

        /** @var EntityManager $entityManager */
        $entityManager = $serviceManager->get(EntityManager::class);
        $controller->setEntityManager($entityManager);

        /** @var ContactService $contactService */
        $contactService = $serviceManager->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var GeneralService $generalService */
        $generalService = $serviceManager->get(GeneralService::class);
        $controller->setGeneralService($generalService);

        /** @var ProjectService $projectService */
        $projectService = $serviceManager->get(ProjectService::class);
        $controller->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $serviceManager->get(OrganisationService::class);
        $controller->setOrganisationService($organisationService);

        /** @var DoaService $doaService */
        $doaService = $serviceManager->get(DoaService::class);
        $controller->setDoaService($doaService);

        /** @var LoiService $loiService */
        $loiService = $serviceManager->get(LoiService::class);
        $controller->setLoiService($loiService);

        /** @var AffiliationService $affiliationService */
        $affiliationService = $serviceManager->get(AffiliationService::class);
        $controller->setAffiliationService($affiliationService);

        /** @var InvoiceService $invoiceService */
        $invoiceService = $serviceManager->get(InvoiceService::class);
        $controller->setInvoiceService($invoiceService);


        return $controller;
    }

    /**
     * @param ServiceLocatorInterface $container
     * @param string                  $canonicalName
     * @param string                  $requestedName
     *
     * @return OrganisationAbstractController
     */
    public function createService(ServiceLocatorInterface $container, $canonicalName = null, $requestedName = null)
    {
        return $this($container, $requestedName);
    }
}
