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

namespace Organisation\View\Helper;

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Interop\Container\ContainerInterface;
use Organisation\Service\ParentService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\I18n\View\Helper\Translate;
use Zend\Router\Http\RouteMatch;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;
use ZfcTwig\View\TwigRenderer;

/**
 * Class AbstractViewHelper
 *
 * @package Content\View\Helper
 */
abstract class AbstractViewHelper extends AbstractHelper
{
    /**
     * @var ContainerInterface
     */
    protected $serviceManager;
    /**
     * @var HelperPluginManager
     */
    protected $helperPluginManager;
    /**
     * @var RouteMatch
     */
    protected $routeMatch = null;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var ParentService
     */
    protected $parentService;
    /**
     * @var AffiliationService
     */
    protected $affiliationService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var VersionService
     */
    protected $versionService;

    /**
     * RouteInterface match returned by the router.
     * Use a test on is_null to have the possibility to overrule the serviceLocator lookup for unit tets reasons.
     *
     * @return RouteMatch.
     */
    public function getRouteMatch()
    {
        if (is_null($this->routeMatch)) {
            $this->routeMatch = $this->getServiceManager()->get('application')->getMvcEvent()->getRouteMatch();
        }

        return $this->routeMatch;
    }

    /**
     * @return ContainerInterface
     */
    public function getServiceManager(): ContainerInterface
    {
        return $this->serviceManager;
    }

    /**
     * @param ContainerInterface $serviceManager
     *
     * @return AbstractViewHelper
     */
    public function setServiceManager($serviceManager): AbstractViewHelper
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * @return TwigRenderer
     */
    public function getRenderer(): TwigRenderer
    {
        return $this->getServiceManager()->get('ZfcTwigRenderer');
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function translate($string): string
    {
        /** @var Translate $translator */
        $translate = $this->getHelperPluginManager()->get('translate');

        return $translate($string);
    }

    /**
     * @return HelperPluginManager
     */
    public function getHelperPluginManager(): HelperPluginManager
    {
        return $this->helperPluginManager;
    }

    /**
     * @param HelperPluginManager $helperPluginManager
     *
     * @return AbstractViewHelper
     */
    public function setHelperPluginManager($helperPluginManager)
    {
        $this->helperPluginManager = $helperPluginManager;

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService(): ContactService
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     *
     * @return AbstractViewHelper
     */
    public function setContactService(ContactService $contactService): AbstractViewHelper
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @return ParentService
     */
    public function getParentService(): ParentService
    {
        return $this->parentService;
    }

    /**
     * @param ParentService $parentService
     *
     * @return AbstractViewHelper
     */
    public function setParentService(ParentService $parentService): AbstractViewHelper
    {
        $this->parentService = $parentService;

        return $this;
    }

    /**
     * @return AffiliationService
     */
    public function getAffiliationService(): AffiliationService
    {
        return $this->affiliationService;
    }

    /**
     * @param AffiliationService $affiliationService
     *
     * @return AbstractViewHelper
     */
    public function setAffiliationService(AffiliationService $affiliationService): AbstractViewHelper
    {
        $this->affiliationService = $affiliationService;

        return $this;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService(): ProjectService
    {
        return $this->projectService;
    }

    /**
     * @param ProjectService $projectService
     *
     * @return AbstractViewHelper
     */
    public function setProjectService(ProjectService $projectService): AbstractViewHelper
    {
        $this->projectService = $projectService;

        return $this;
    }

    /**
     * @return VersionService
     */
    public function getVersionService(): VersionService
    {
        return $this->versionService;
    }

    /**
     * @param VersionService $versionService
     *
     * @return AbstractViewHelper
     */
    public function setVersionService(VersionService $versionService): AbstractViewHelper
    {
        $this->versionService = $versionService;

        return $this;
    }
}
