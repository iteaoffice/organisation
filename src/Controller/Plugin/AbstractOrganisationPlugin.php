<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller\Plugin;

use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Invoice\Service\InvoiceService;
use Organisation\Options\ModuleOptions;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Service\CallService;
use Program\Service\ProgramService;
use Project\Service\ProjectService;
use Project\Service\VersionService;
use Zend\Http\Request;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Router\Http\RouteMatch;
use ZfcTwig\View\TwigRenderer;

/**
 * Class HandleImport.
 */
abstract class AbstractOrganisationPlugin extends AbstractPlugin
{
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var VersionService
     */
    protected $versionService;
    /**
     * @var OrganisationService;
     */
    protected $organisationService;
    /**
     * @var ParentService
     */
    protected $parentService;
    /**
     * @var AffiliationService
     */
    protected $affiliationService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var ProgramService
     */
    protected $programService;
    /**
     * @var CallService
     */
    protected $callService;
    /**
     * @var InvoiceService
     */
    protected $invoiceService;
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var TwigRenderer
     */
    protected $twigRenderer;
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    /**
     * @var Request
     */
    protected $request;

    /**
     * @return GeneralService
     */
    public function getGeneralService(): GeneralService
    {
        return $this->generalService;
    }

    /**
     * @param GeneralService $generalService
     *
     * @return AbstractOrganisationPlugin
     */
    public function setGeneralService(GeneralService $generalService): AbstractOrganisationPlugin
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * @return OrganisationService
     */
    public function getOrganisationService(): OrganisationService
    {
        return $this->organisationService;
    }

    /**
     * @param OrganisationService $organisationService
     *
     * @return AbstractOrganisationPlugin
     */
    public function setOrganisationService(OrganisationService $organisationService): AbstractOrganisationPlugin
    {
        $this->organisationService = $organisationService;

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
     * @return AbstractOrganisationPlugin
     */
    public function setParentService(ParentService $parentService): AbstractOrganisationPlugin
    {
        $this->parentService = $parentService;

        return $this;
    }

    /**
     * @return InvoiceService
     */
    public function getInvoiceService(): InvoiceService
    {
        return $this->invoiceService;
    }

    /**
     * @param InvoiceService $invoiceService
     *
     * @return AbstractOrganisationPlugin
     */
    public function setInvoiceService(InvoiceService $invoiceService): AbstractOrganisationPlugin
    {
        $this->invoiceService = $invoiceService;

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
     * @return AbstractOrganisationPlugin
     */
    public function setContactService(ContactService $contactService): AbstractOrganisationPlugin
    {
        $this->contactService = $contactService;

        return $this;
    }

    /**
     * @return ProgramService
     */
    public function getProgramService(): ProgramService
    {
        return $this->programService;
    }

    /**
     * @param ProgramService $programService
     *
     * @return AbstractOrganisationPlugin
     */
    public function setProgramService(ProgramService $programService): AbstractOrganisationPlugin
    {
        $this->programService = $programService;

        return $this;
    }

    /**
     * @return CallService
     */
    public function getCallService(): CallService
    {
        return $this->callService;
    }

    /**
     * @param CallService $callService
     *
     * @return AbstractOrganisationPlugin
     */
    public function setCallService(CallService $callService): AbstractOrganisationPlugin
    {
        $this->callService = $callService;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return AbstractOrganisationPlugin
     */
    public function setEntityManager(EntityManager $entityManager): AbstractOrganisationPlugin
    {
        $this->entityManager = $entityManager;

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
     * @return AbstractOrganisationPlugin
     */
    public function setProjectService(ProjectService $projectService): AbstractOrganisationPlugin
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
     * @return AbstractOrganisationPlugin
     */
    public function setVersionService(VersionService $versionService): AbstractOrganisationPlugin
    {
        $this->versionService = $versionService;

        return $this;
    }


    /**
     * @return ModuleOptions
     */
    public function getModuleOptions(): ModuleOptions
    {
        return $this->moduleOptions;
    }

    /**
     * @param ModuleOptions $moduleOptions
     * @return AbstractOrganisationPlugin
     */
    public function setModuleOptions(ModuleOptions $moduleOptions): AbstractOrganisationPlugin
    {
        $this->moduleOptions = $moduleOptions;

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
     * @return AbstractOrganisationPlugin
     */
    public function setAffiliationService(AffiliationService $affiliationService): AbstractOrganisationPlugin
    {
        $this->affiliationService = $affiliationService;

        return $this;
    }

    /**
     * @return TwigRenderer
     */
    public function getTwigRenderer(): TwigRenderer
    {
        return $this->twigRenderer;
    }

    /**
     * @param TwigRenderer $twigRenderer
     * @return AbstractOrganisationPlugin
     */
    public function setTwigRenderer(TwigRenderer $twigRenderer): AbstractOrganisationPlugin
    {
        $this->twigRenderer = $twigRenderer;

        return $this;
    }

    /**
     * @return RouteMatch
     */
    public function getRouteMatch(): RouteMatch
    {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     * @return AbstractOrganisationPlugin
     */
    public function setRouteMatch(RouteMatch $routeMatch): AbstractOrganisationPlugin
    {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return AbstractOrganisationPlugin
     */
    public function setRequest(Request $request): AbstractOrganisationPlugin
    {
        $this->request = $request;

        return $this;
    }
}
