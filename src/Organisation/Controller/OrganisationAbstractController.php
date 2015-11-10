<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */
namespace Organisation\Controller;

use Affiliation\Service\AffiliationService;
use Affiliation\Service\DoaService;
use Affiliation\Service\LoiService;
use BjyAuthorize\Controller\Plugin\IsAllowed;
use Contact\Service\ContactService;
use General\Service\GeneralService;
use Invoice\Controller\Plugin\GetFilter as InvoiceFilterPlugin;
use Invoice\Service\InvoiceService;
use Organisation\Controller\Plugin\GetFilter as OrganisationFilterPlugin;
use Organisation\Service\FormService;
use Organisation\Service\FormServiceAwareInterface;
use Organisation\Service\OrganisationService;
use Organisation\Service\OrganisationServiceAwareInterface;
use Project\Service\ProjectService;
use Zend\I18n\View\Helper\Translate;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

/**
 * @category    Organisation
 * @package     Controller
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      IsAllowed isAllowed($resource, $action)
 * @method InvoiceFilterPlugin getInvoiceFilter
 * @method OrganisationFilterPlugin getOrganisationFilter
 */
abstract class OrganisationAbstractController extends AbstractActionController implements FormServiceAwareInterface, OrganisationServiceAwareInterface
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;
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
     * Gateway to the Organisation Service
     *
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->organisationService;
    }

    /**
     * @param $organisationService
     *
     * @return OrganisationAbstractController
     */
    public function setOrganisationService(
        OrganisationService $organisationService
    ) {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return GeneralService
     */
    public function getGeneralService()
    {
        return $this->generalService;
    }

    /**
     * @param GeneralService $generalService
     *
     * @return OrganisationAbstractController
     */
    public function setGeneralService(GeneralService $generalService)
    {
        $this->generalService = $generalService;

        return $this;
    }

    /**
     * @return \Organisation\Service\FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return OrganisationController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * Proxy for the flash messenger helper to have the string translated earlier
     *
     * @param $string
     *
     * @return string
     */
    protected function translate($string)
    {
        /**
         * @var $translate Translate
         */
        $translate = $this->getServiceLocator()->get('ViewHelperManager')
            ->get('translate');

        return $translate($string);
    }

    /**
     * @return InvoiceService
     */
    public function getInvoiceService()
    {
        return $this->invoiceService;
    }

    /**
     * @param  InvoiceService $invoiceService
     *
     * @return OrganisationAbstractController
     */
    public function setInvoiceService(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;

        return $this;
    }

    /**
     * @return ProjectService
     */
    public function getProjectService()
    {
        return $this->projectService;
    }

    /**
     * @param  ProjectService $projectService
     *
     * @return OrganisationAbstractController
     */
    public function setProjectService(ProjectService $projectService)
    {
        $this->projectService = $projectService;

        return $this;
    }

    /**
     * @return AffiliationService
     */
    public function getAffiliationService()
    {
        return $this->affiliationService;
    }

    /**
     * @param  AffiliationService $affiliationService
     *
     * @return OrganisationAbstractController
     */
    public function setAffiliationService(
        AffiliationService $affiliationService
    ) {
        $this->affiliationService = $affiliationService;

        return $this;
    }

    /**
     * @return DoaService
     */
    public function getDoaService()
    {
        return $this->doaService;
    }

    /**
     * @param  DoaService $doaService
     *
     * @return OrganisationAbstractController
     */
    public function setDoaService(DoaService $doaService)
    {
        $this->doaService = $doaService;

        return $this;
    }

    /**
     * @return LoiService
     */
    public function getLoiService()
    {
        return $this->loiService;
    }

    /**
     * @param  LoiService $loiService
     *
     * @return OrganisationAbstractController
     */
    public function setLoiService(LoiService $loiService)
    {
        $this->loiService = $loiService;

        return $this;
    }

    /**
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->contactService;
    }

    /**
     * @param  ContactService $contactService
     *
     * @return OrganisationAbstractController
     */
    public function setContactService(ContactService $contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }
}
