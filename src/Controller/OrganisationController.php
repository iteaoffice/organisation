<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * @category    Organisation
 */
class OrganisationController extends OrganisationAbstractController
{
    /**
     * @return ViewModel
     */
    public function searchAction(): ViewModel
    {
        $searchItem = $this->getRequest()->getQuery()->get('search_item');
        $maxResults = $this->getRequest()->getQuery()->get('max_rows', 12);
        $countryId = $this->getRequest()->getQuery()->get('country');
        $searchResult = $this->getOrganisationService()->searchOrganisation($searchItem, $maxResults, $countryId);
        /**
         * Include a paginator to be able to have later paginated search results in pages
         */
        $paginator = new Paginator(new ArrayAdapter($searchResult));
        $paginator::setDefaultItemCountPerPage($maxResults);
        $paginator->setCurrentPageNumber(1);
        $paginator->setPageRange(1);
        $viewModel = new ViewModel(
            [
                'paginator'           => $paginator,
                'organisationService' => $this->getOrganisationService(),
            ]
        );
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('organisation/partial/list/organisation-search');

        return $viewModel;
    }
}
