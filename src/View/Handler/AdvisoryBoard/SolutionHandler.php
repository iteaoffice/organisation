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
use Organisation\Entity\AdvisoryBoard\Solution;
use Organisation\Search\Service\AdvisoryBoard\SolutionSearchService;
use Organisation\Service\AdvisoryBoard\SolutionService;
use Search\Paginator\Adapter\SolariumPaginator;
use ZfcTwig\View\TwigRenderer;

use function sprintf;

/**
 * Class OrganisationHandler
 * @package Organisation\View\Handler
 */
final class SolutionHandler extends AbstractHandler
{
    private SolutionService $solutionService;
    private SolutionSearchService $solutionSearchService;

    public function __construct(
        Application $application,
        HelperPluginManager $helperPluginManager,
        TwigRenderer $renderer,
        AuthenticationService $authenticationService,
        TranslatorInterface $translator,
        SolutionService $solutionService,
        SolutionSearchService $solutionSearchService
    ) {
        parent::__construct(
            $application,
            $helperPluginManager,
            $renderer,
            $authenticationService,
            $translator
        );

        $this->solutionService       = $solutionService;
        $this->solutionSearchService = $solutionSearchService;
    }

    public function __invoke(Content $content): ?string
    {
        $params = $this->extractContentParam($content);

        $solution = $this->getSolutionByParams($params);

        switch ($content->getHandler()->getHandler()) {
            case 'advisory_board_solution_list':
                return $this->parseSolutionList();
            default:
                return sprintf(
                    'No handler available for <code>%s</code> in class <code>%s</code>',
                    $content->getHandler()->getHandler(),
                    __CLASS__
                );
        }
    }

    private function getSolutionByParams(array $params): ?Solution
    {
        $solution = null;

        if (null !== $params['id']) {
            $solution = $this->solutionService->findSolutionById((int)$params['id']);
        }

        return $solution;
    }

    private function parseSolutionList(): string
    {
        $this->solutionSearchService->setPublicSearch('');

        $paginator = new Paginator(
            new SolariumPaginator(
                $this->solutionSearchService->getSolrClient(),
                $this->solutionSearchService->getQuery()
            )
        );
        $paginator::setDefaultItemCountPerPage(1000);
        $paginator->setCurrentPageNumber(1);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return $this->renderer->render(
            'cms/organisation/advisory-board/solution/list',
            [
                'paginator'       => $paginator,
                'solutionService' => $this->solutionService
            ]
        );
    }
}
