<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Invoice\Controller\Plugin\GetFilter as InvoiceFilterPlugin;
use Invoice\Service\InvoiceService;
use Organisation\Controller\Plugin\GetFilter as OrganisationFilterPlugin;
use Organisation\Controller\Plugin\HandleParentAndProjectImport;
use Organisation\Controller\Plugin\HandleParentImport;
use Organisation\Controller\Plugin\MergeOrganisation;
use Organisation\Controller\Plugin\RenderOverviewVariableContributionSheet;
use Organisation\Controller\Plugin\RenderOverviewExtraVariableContributionSheet;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Project\Service\ProjectService;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\View\HelperPluginManager;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Organisation
 * @package     Controller
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 * @method      InvoiceFilterPlugin getInvoiceFilter()
 * @method      OrganisationFilterPlugin getOrganisationFilter()
 * @method      RenderOverviewVariableContributionSheet renderOverviewVariableContributionSheet($parent, $year)
 * @method      RenderOverviewExtraVariableContributionSheet renderOverviewExtraVariableContributionSheet($parent, $year)
 * @method      HandleParentAndProjectImport handleParentAndProjectImport($fileData, $keys, $doImport)
 * @method      HandleParentImport handleParentImport($fileData, $keys, $doImport)
 * @method      MergeOrganisation mergeOrganisation()
 */
abstract class OrganisationAbstractController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var ParentService
     */
    protected $parentService;
    /**
     * @var DoaService
     */
    protected $doaService;
    /**
     * @var LoiService
     */
    protected $loiService;
    /**
     * @var AffiliationService;
     */
    protected $affiliationService;
    /**
     * @var InvoiceService;
     */
    protected $invoiceService;
    /**
     * @var ProjectService
     */
    protected $projectService;
    /**
     * @var GeneralService
     */
    protected $generalService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var HelperPluginManager
     */
    protected $viewHelperManager;

    /**
     * Gateway to the Organisation Service
     *
     * @return OrganisationService
     */
    public function getOrganisationService(): OrganisationService
    {
        return $this->organisationService;
    }

    /**
     * @param $organisationService
     *
     * @return OrganisationAbstractController
     */
    public function setOrganisationService($organisationService): OrganisationAbstractController
    {
        $this->organisationService = $organisationService;

        return $this;
    }

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
     * @return OrganisationAbstractController
     */
    public function setGeneralService($generalService): OrganisationAbstractController
    {
        $this->generalService = $generalService;

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
     * @return OrganisationAbstractController
     */
    public function setParentService(ParentService $parentService): OrganisationAbstractController
    {
        $this->parentService = $parentService;

        return $this;
    }

    /**
     * @return \Organisation\Service\FormService
     */
    public function getFormService(): FormService
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return OrganisationAbstractController
     */
    public function setFormService($formService): OrganisationAbstractController
    {
        $this->formService = $formService;

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
     * @param  InvoiceService $invoiceService
     *
     * @return OrganisationAbstractController
     */
    public function setInvoiceService($invoiceService): OrganisationAbstractController
    {
        $this->invoiceService = $invoiceService;

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
     * @param  ProjectService $projectService
     *
     * @return OrganisationAbstractController
     */
    public function setProjectService($projectService): OrganisationAbstractController
    {
        $this->projectService = $projectService;

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
     * @param  AffiliationService $affiliationService
     *
     * @return OrganisationAbstractController
     */
    public function setAffiliationService($affiliationService): OrganisationAbstractController
    {
        $this->affiliationService = $affiliationService;

        return $this;
    }

    /**
     * @return DoaService
     */
    public function getDoaService(): DoaService
    {
        return $this->doaService;
    }

    /**
     * @param  DoaService $doaService
     *
     * @return OrganisationAbstractController
     */
    public function setDoaService($doaService): OrganisationAbstractController
    {
        $this->doaService = $doaService;

        return $this;
    }

    /**
     * @return LoiService
     */
    public function getLoiService(): LoiService
    {
        return $this->loiService;
    }

    /**
     * @param  LoiService $loiService
     *
     * @return OrganisationAbstractController
     */
    public function setLoiService($loiService): OrganisationAbstractController
    {
        $this->loiService = $loiService;

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
     * @param  ContactService $contactService
     *
     * @return OrganisationAbstractController
     */
    public function setContactService($contactService): OrganisationAbstractController
    {
        $this->contactService = $contactService;

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
     * @return OrganisationAbstractController
     */
    public function setEntityManager($entityManager): OrganisationAbstractController
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Proxy for the flash messenger helper to have the string translated earlier
     *
     * @param $string
     *
     * @return string
     */
    protected function translate($string): string
    {
        /**
         * @var $translate Translate
         */
        $translate = $this->getViewHelperManager()->get('translate');

        return $translate($string);
    }

    /**
     * @return HelperPluginManager
     */
    public function getViewHelperManager(): HelperPluginManager
    {
        return $this->viewHelperManager;
    }

    /**
     * @param HelperPluginManager $viewHelperManager
     *
     * @return OrganisationAbstractController
     */
    public function setViewHelperManager(HelperPluginManager $viewHelperManager): OrganisationAbstractController
    {
        $this->viewHelperManager = $viewHelperManager;

        return $this;
    }
}
