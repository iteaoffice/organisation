<?php
/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\View\Factory;

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Invoice\Service\InvoiceService;
use Organisation\Service\ParentService;
use Organisation\View\Helper\AbstractViewHelper;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class LinkInvokableFactory
 *
 * @package Partner\View\Factory
 */
final class ViewHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractViewHelper
    {
        /** @var AbstractViewHelper $viewHelper */
        $viewHelper = new $requestedName($options);
        $viewHelper->setServiceManager($container);
        $viewHelper->setHelperPluginManager($container->get('ViewHelperManager'));

        $viewHelper->setTranslator($container->get(TranslatorInterface::class));

        /** @var ContactService $contactService */
        $contactService = $container->get(ContactService::class);
        $viewHelper->setContactService($contactService);

        /** @var ParentService $parentService */
        $parentService = $container->get(ParentService::class);
        $viewHelper->setParentService($parentService);

        /** @var ProjectService $projectService */
        $projectService = $container->get(ProjectService::class);
        $viewHelper->setProjectService($projectService);

        /** @var VersionService $versionService */
        $versionService = $container->get(VersionService::class);
        $viewHelper->setVersionService($versionService);

        /** @var InvoiceService $invoiceService */
        $invoiceService = $container->get(InvoiceService::class);
        $viewHelper->setInvoiceService($invoiceService);

        /** @var AffiliationService $affiliationService */
        $affiliationService = $container->get(AffiliationService::class);
        $viewHelper->setAffiliationService($affiliationService);

        return $viewHelper;
    }
}
