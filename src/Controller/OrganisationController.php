<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\Controller;

use Organisation\Entity\Logo;
use PHPThumb\GD;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * @category    Organisation
 */
class OrganisationController extends OrganisationAbstractController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function logoAction()
    {
        /**
         * @var $logo Logo
         */
        $logo = $this->getOrganisationService()->findEntityById(Logo::class, $this->params('id'));

        /**
         * Do a check if the given has is correct to avoid guessing the image
         */
        if (is_null($logo)
            || $this->params('hash') !== $logo->getHash()
        ) {
            return $this->notFoundAction();
        }

        $file = stream_get_contents($logo->getOrganisationLogo());
        $width = $this->params('width', null);

        /*
         * Check if the file is cached and if not, create it
         */
        if (!file_exists($logo->getCacheFileName($width))) {
            /*
             * The file exists, but is it not updated?
             */
            file_put_contents($logo->getCacheFileName($width), $file);

            //Start the resize-action based on the width
            if (!is_null($width)) {
                $thumb = new GD($logo->getCacheFileName($width));
                $thumb->resize($width);
                $thumb->save($logo->getCacheFileName($width));
            }
        }


        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine("Pragma: public")
            ->addHeaderLine('Content-Type: ' . $logo->getContentType()->getContentType())
            ->addHeaderLine('Content-Length: ' . (string)strlen(file_get_contents($logo->getCacheFileName($width))));
        $response->setContent(file_get_contents($logo->getCacheFileName($width)));

        return $response;
    }

    /**
     * @return ViewModel
     */
    public function searchAction()
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
