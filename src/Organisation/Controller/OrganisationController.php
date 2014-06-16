<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;

use Organisation\Service\OrganisationService;
use Organisation\Service\FormServiceAwareInterface;
use Organisation\Service\FormService;
use Organisation\Form\Search;

/**
 * @category    Organisation
 * @package     Controller
 */
class OrganisationController extends AbstractActionController implements
    FormServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Message container
     * @return array|void
     */
    public function indexAction()
    {
    }

    /**
     * Give a list of organisations
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationsAction()
    {
        $organisations = $this->getOrganisationService()->findAll('organisation');

        return new ViewModel(array('organisations' => $organisations));
    }

    /**
     * Show the details of 1 organisation
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationAction()
    {
        $organisation = $this->getOrganisationService()->findEntityById(
            'organisation',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('organisation' => $organisation));
    }

    /**
     * Show the details of 1 organisation
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function logoAction()
    {
        $response = $this->getResponse();

        /**
         * Return null when no id can be found
         */
        if (is_null($this->getEvent()->getRouteMatch()->getParam('id', null))) {
            return $response;
        }

        $logo = $this->getOrganisationService()->findEntityById(
            'logo',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        /**
         * Return null when no image can be found
         */
        if (is_null($logo)) {
            return $response;
        }

        $file = stream_get_contents($logo->getOrganisationLogo());

        /**
         * Create a cache-version of the file
         */
        if (!file_exists($logo->getCacheFileName())) {
            //Save a copy of the file in the caching-folder
            file_put_contents($logo->getCacheFileName(), $file);
        }

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Type: ' . $logo->getContentType()->getContentType())
            ->addHeaderLine('Content-Length: ' . (string) strlen($file));

        $response->setContent($file);

        return $response;
    }

    /**
     * @return ViewModel
     */
    public function searchAction()
    {
        $searchItem = $this->getRequest()->getQuery()->get('search_item');
        $maxResults = $this->getRequest()->getQuery()->get('max_rows');
        $countryId  = $this->getRequest()->getQuery()->get('country');

        $searchResult = $this->getOrganisationService()->searchOrganisation($searchItem, $maxResults, $countryId);

        /**
         * Include a paginator to be able to have later paginated search results in pages
         */
        $paginator = new Paginator(new ArrayAdapter($searchResult));
        $paginator->setDefaultItemCountPerPage($maxResults);
        $paginator->setCurrentPageNumber(1);
        $paginator->setPageRange(1);

        $viewModel = new ViewModel(array('paginator' => $paginator));
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('organisation/partial/list/organisation-search');

        return $viewModel;
    }

    /**
     * @return FormService
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
     * Gateway to the Organisation Service
     *
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceLocator()->get('organisation_organisation_service');
    }

    /**
     * @param $organisationService
     *
     * @return OrganisationController
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }
}
