<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\View\Handler\AdvisoryBoard;

use Content\Entity\Content;
use General\View\Handler\AbstractHandler;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Application;
use Laminas\Paginator\Paginator;
use Laminas\View\HelperPluginManager;
use Organisation\Entity\AdvisoryBoard\City;
use Organisation\Search\Service\AdvisoryBoard\CitySearchService;
use Organisation\Service\AdvisoryBoard\CityService;
use Search\Paginator\Adapter\SolariumPaginator;
use ZfcTwig\View\TwigRenderer;

use function sprintf;

/**
 * Class OrganisationHandler
 * @package Organisation\View\Handler
 */
final class CityHandler extends AbstractHandler
{
    private CityService $cityService;
    private CitySearchService $citySearchService;

    public function __construct(
        Application $application,
        HelperPluginManager $helperPluginManager,
        TwigRenderer $renderer,
        AuthenticationService $authenticationService,
        TranslatorInterface $translator,
        CityService $cityService,
        CitySearchService $citySearchService
    ) {
        parent::__construct(
            $application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $translator
        );

        $this->cityService       = $cityService;
        $this->citySearchService = $citySearchService;
    }

    public function __invoke(Content $content): ?string
    {
        $params = $this->extractContentParam($content);

        $city = $this->getCityByParams($params);

        switch ($content->getHandler()->getHandler()) {
            case 'advisory_board_city_list':
                return $this->parseCityList();
            case 'advisory_board_city_tenderwebsite_list':
                return $this->parseCityTenderWebsiteList();
            default:
                return sprintf(
                    'No handler available for <code>%s</code> in class <code>%s</code>',
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    private function getCityByParams(array $params): ?City
    {
        $city = null;

        if (null !== $params['id']) {
            $city = $this->cityService->findCityById((int)$params['id']);
        }

        return $city;
    }

    private function parseCityList(): string
    {
        $this->citySearchService->setPublicSearch('');

        $paginator = new Paginator(
            new SolariumPaginator(
                $this->citySearchService->getSolrClient(),
                $this->citySearchService->getQuery()
            )
        );
        $paginator::setDefaultItemCountPerPage(1000);
        $paginator->setCurrentPageNumber(1);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));


        return $this->renderer->render(
            'cms/organisation/advisory-board/city/list',
            [
                'paginator'   => $paginator,
                'cityService' => $this->cityService
            ]
        );
    }

    private function parseCityTenderWebsiteList(): string
    {
        $this->citySearchService->setPublicTenderSearch('');

        $paginator = new Paginator(
            new SolariumPaginator(
                $this->citySearchService->getSolrClient(),
                $this->citySearchService->getQuery()
            )
        );
        $paginator::setDefaultItemCountPerPage(1000);
        $paginator->setCurrentPageNumber(1);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return $this->renderer->render(
            'cms/organisation/advisory-board/city/list-tender-website',
            [
                'paginator'   => $paginator,
                'cityService' => $this->cityService
            ]
        );
    }
}
