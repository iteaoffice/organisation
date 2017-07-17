<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */
declare(strict_types=1);

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
use Organisation\Service\ParentService;
use Project\Service\ProjectService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\HelperPluginManager;

/**
 * Class ControllerFactory
 *
 * @package Project\Controller\Factory
 */
final class ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return OrganisationAbstractController
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): OrganisationAbstractController {
        /** @var OrganisationAbstractController $controller */
        $controller = new $requestedName($options);

        /** @var FormService $formService */
        $formService = $container->get(FormService::class);
        $controller->setFormService($formService);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $controller->setEntityManager($entityManager);

        /** @var ParentService $parentService */
        $parentService = $container->get(ParentService::class);
        $controller->setParentService($parentService);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $controller->setContactService($contactService);

        /** @var GeneralService $generalService */
        $generalService = $container->get(GeneralService::class);
        $controller->setGeneralService($generalService);

        /** @var ProjectService $projectService */
        $projectService = $container->get(ProjectService::class);
        $controller->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $controller->setOrganisationService($organisationService);

        /** @var DoaService $doaService */
        $doaService = $container->get(DoaService::class);
        $controller->setDoaService($doaService);

        /** @var LoiService $loiService */
        $loiService = $container->get(LoiService::class);
        $controller->setLoiService($loiService);

        /** @var AffiliationService $affiliationService */
        $affiliationService = $container->get(AffiliationService::class);
        $controller->setAffiliationService($affiliationService);

        /** @var InvoiceService $invoiceService */
        $invoiceService = $container->get(InvoiceService::class);
        $controller->setInvoiceService($invoiceService);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $controller->setViewHelperManager($viewHelperManager);


        return $controller;
    }
}
