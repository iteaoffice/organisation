<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\Controller;

use Organisation\Entity\Logo;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * @category    Organisation
 */
class OrganisationController extends OrganisationAbstractController
{
    /**
     * Show the details of 1 organisation.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function logoAction()
    {
        /**
         * @var $logo Logo
         */
        $logo = $this->getOrganisationService()->findEntityById(
            'logo',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        /**
         * Do a check if the given has is correct to avoid guessing the image
         */
        if (is_null($logo) || $this->getEvent()->getRouteMatch()->getParam('hash') !== $logo->getHash()) {
            return $this->notFoundAction();
        }

        $file = stream_get_contents($logo->getOrganisationLogo());

        /*
         * Check if the file is cached and if not, create it
         */
        if (!file_exists($logo->getCacheFileName())) {
            /*
             * The file exists, but is it not updated?
             */
            file_put_contents($logo->getCacheFileName(), $file);
        }

        $response = $this->getResponse();
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
        $countryId = $this->getRequest()->getQuery()->get('country');
        $searchResult = $this->getOrganisationService()->searchOrganisation($searchItem, $maxResults, $countryId);
        /*
         * Include a paginator to be able to have later paginated search results in pages
         */
        $paginator = new Paginator(new ArrayAdapter($searchResult));
        $paginator->setDefaultItemCountPerPage($maxResults);
        $paginator->setCurrentPageNumber(1);
        $paginator->setPageRange(1);
        $viewModel = new ViewModel(['paginator' => $paginator]);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('organisation/partial/list/organisation-search');

        return $viewModel;
    }
}
