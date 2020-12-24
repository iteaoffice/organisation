<?php

/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Entity;
use Organisation\Service\BoardService;
use Organisation\Service\FormService;
use Search\Form\SearchFilter;

/**
 * Class BoardController
 * @package Organisation\Controller
 */
final class BoardController extends OrganisationAbstractController
{
    private BoardService $boardService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(BoardService $boardService, FormService $formService, TranslatorInterface $translator)
    {
        $this->boardService = $boardService;
        $this->formService  = $formService;
        $this->translator   = $translator;
    }


    public function listAction(): ViewModel
    {
        $page              = $this->params()->fromRoute('page', 1);
        $filterPlugin      = $this->getOrganisationFilter();
        $organisationQuery = $this->boardService->findFiltered(Entity\Board::class, $filterPlugin->getFilter());

        $paginator
            = new Paginator(new PaginatorAdapter(new ORMPaginator($organisationQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'     => $paginator,
                'form'          => $form,
                'encodedFilter' => urlencode($filterPlugin->getHash()),
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
            ]
        );
    }

    public function newAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Entity\Board::class, $data);
        $form->remove('delete');


        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/board/list');
            }

            if ($form->isValid()) {
                /* @var $board Entity\Board */
                $board = $form->getData();

                $result = $this->boardService->save($board);
                return $this->redirect()->toRoute(
                    'zfcadmin/board/view',
                    [
                        'id' => $result->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        $board = $this->boardService->find(Entity\Board::class, (int)$this->params('id'));

        if (null === $board) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($board, $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/board/view',
                    [
                        'id' => $board->getId(),
                    ]
                );
            }

            if (isset($data['delete'])) {
                $this->boardService->delete($board);
                return $this->redirect()->toRoute(
                    'zfcadmin/board/list',
                    [
                        'id' => $board->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /* @var $board Entity\Board */
                $board = $form->getData();

                $this->boardService->save($board);
                return $this->redirect()->toRoute(
                    'zfcadmin/board/view',
                    [
                        'id' => $board->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function viewAction(): ViewModel
    {
        $board = $this->boardService->find(Entity\Board::class, (int)$this->params('id'));

        if (null === $board) {
            return $this->notFoundAction();
        }

        return new ViewModel(['board' => $board]);
    }
}
