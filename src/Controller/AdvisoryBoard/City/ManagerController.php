<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\AdvisoryBoard\City;

use General\Service\GeneralService;
use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Search\Service\AdvisoryBoard\CitySearchService;
use Organisation\Service\AdvisoryBoard\CityService;
use Organisation\Service\FormService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;

final class ManagerController extends AbstractController
{
    private CityService $cityService;
    private CitySearchService $searchService;
    private GeneralService $generalService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(CityService $cityService, CitySearchService $searchService, GeneralService $generalService, FormService $formService, TranslatorInterface $translator)
    {
        $this->cityService    = $cityService;
        $this->searchService  = $searchService;
        $this->generalService = $generalService;
        $this->formService    = $formService;
        $this->translator     = $translator;
    }


    public function listAction(): ViewModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $page    = $this->params('page', 1);
        $form    = new SearchResult();
        $data    = array_merge(
            [
                'order'     => '',
                'direction' => '',
                'query'     => '',
                'facet'     => [],
            ],
            $request->getQuery()->toArray()
        );

        if ($request->isGet()) {
            $this->searchService->setSearch($data['query'], [], $data['order'], $data['direction']);
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = sprintf('"%s"', $value);
                    }

                    $this->searchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->searchService->getQuery()->getFacetSet(),
                $this->searchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator($this->searchService->getSolrClient(), $this->searchService->getQuery())
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000 : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel(
            [
                'form'        => $form,
                'order'       => $data['order'],
                'direction'   => $data['direction'],
                'query'       => $data['query'],
                'badges'      => $form->getBadges(),
                'arguments'   => http_build_query($form->getFilteredData()),
                'paginator'   => $paginator,
                'cityService' => $this->cityService
            ]
        );
    }

    public function newAction()
    {
        $data = array_merge(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form = $this->formService->prepare(new Entity\AdvisoryBoard\City(), $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/advisory-board/city/list');
            }

            if ($form->isValid()) {
                /* @var $city Entity\AdvisoryBoard\City */
                $city = $form->getData();

                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['file']['name'])) {
                    $image = new Entity\AdvisoryBoard\City\Image();
                    $image->setCity($city);
                    $image->setImage(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $image->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                    $city->setImage($image);
                }

                $city = $this->cityService->save($city);

                $this->flashMessenger()->addSuccessMessage($this->translator->translate("txt-city-has-been-created-successfully"));

                return $this->redirect()->toRoute(
                    'zfcadmin/advisory-board/city/details/general',
                    [
                        'id' => $city->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        $city = $this->cityService->findCityById((int)$this->params('id'));

        if (null === $city) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = $this->formService->prepare($city, $data);

        if (!$this->cityService->canDeleteCity($city)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/advisory-board/city/details/general',
                    [
                        'id' => $city->getId(),
                    ]
                );
            }

            if (isset($data['delete']) && $this->cityService->canDeleteCity($city)) {
                $this->cityService->delete($city);

                $this->flashMessenger()->addSuccessMessage($this->translator->translate("txt-city-has-been-deleted-successfully"));

                return $this->redirect()->toRoute('zfcadmin/advisory-board/city/list');
            }

            if ($form->isValid()) {
                /** @var Entity\AdvisoryBoard\City $city */
                $city = $form->getData();

                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['file']['name'])) {
                    $image = $city->getImage();
                    if (null === $image) {
                        // Create a new logo element
                        $image = new Entity\AdvisoryBoard\City\Image();
                        $image->setCity($city);
                    }
                    $image->setImage(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $image->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                    $city->setImage($image);
                }

                $this->cityService->save($city);

                $this->flashMessenger()->addSuccessMessage($this->translator->translate("txt-city-has-been-saved-successfully"));

                return $this->redirect()->toRoute(
                    'zfcadmin/advisory-board/city/details/general',
                    [
                        'id' => $city->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'type' => $city]);
    }
}
