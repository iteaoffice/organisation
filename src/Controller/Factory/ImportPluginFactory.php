<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */
namespace Organisation\Controller\Factory;

use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Interop\Container\ContainerInterface;
use Organisation\Controller\Plugin\AbstractImportPlugin;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PluginFactory
 *
 * @package Contact\Controller\Factory
 */
final class ImportPluginFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|PluginManager $container
     * @param string                           $requestedName
     * @param array|null                       $options
     *
     * @return AbstractImportPlugin
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractImportPlugin
    {
        /** @var AbstractImportPlugin $plugin */
        $plugin = new $requestedName($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $plugin->setEntityManager($entityManager);

        /** @var GeneralService $generalService */
        $generalService = $container->get(GeneralService::class);
        $plugin->setGeneralService($generalService);

        /** @var ProjectService $projectService */
        $projectService = $container->get(ProjectService::class);
        $plugin->setProjectService($projectService);

        /** @var OrganisationService $organisationService */
        $organisationService = $container->get(OrganisationService::class);
        $plugin->setOrganisationService($organisationService);

        /** @var ParentService $parentService */
        $parentService = $container->get(ParentService::class);
        $plugin->setParentService($parentService);

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $plugin->setContactService($contactService);

        /** @var ProgramService $programService */
        $programService = $container->get(ProgramService::class);
        $plugin->setProgramService($programService);

        /** @var CallService $callService */
        $callService = $container->get(CallService::class);
        $plugin->setCallService($callService);

        return $plugin;
    }
}
