<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category  Publication
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2016 ITEA Office (http://itea3.org)
 */

namespace Organisation\Controller\Factory;

use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Invoice\Service\InvoiceService;
use Member\Service\FormService;
use Organisation\Controller\OrganisationAbstractController;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ControllerInvokableAbstractFactory
 *
 * @package Organisation\Acl\Factory
 */
class ControllerInvokableAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (class_exists($requestedName)
            && in_array(OrganisationAbstractController::class, class_parents($requestedName)));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface|ControllerManager $serviceLocator
     * @param string                                    $name
     * @param string                                    $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        try {

            /** @var OrganisationAbstractController $controller */
            $controller = new $requestedName();
            $controller->setServiceLocator($serviceLocator);

            $serviceManager = $serviceLocator->getServiceLocator();

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
        } catch (\Exception $e) {
            var_dump($e);
            die();
        }
    }
}
