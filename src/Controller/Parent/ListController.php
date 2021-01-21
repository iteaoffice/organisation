<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Parent;

use Contact\Service\ContactService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Http\Headers;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;

use function mb_convert_encoding;
use function ob_get_clean;
use function strlen;

/**
 * Class ParentController
 * @package Organisation\Controller
 */
final class ListController extends AbstractController
{
    private ParentService $parentService;
    private OrganisationService $organisationService;
    private ContactService $contactService;
    private EntityManager $entityManager;
    private TranslatorInterface $translator;

    public function __construct(
        ParentService $parentService,
        OrganisationService $organisationService,
        ContactService $contactService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->parentService       = $parentService;
        $this->organisationService = $organisationService;
        $this->contactService      = $contactService;
        $this->entityManager       = $entityManager;
        $this->translator          = $translator;
    }

    public function parentAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $query        = $this->parentService->findFiltered(
            Entity\ParentEntity::class,
            $filterPlugin->getFilter()
        );

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($query, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilterForm($this->entityManager);

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'     => $paginator,
                'form'          => $form,
                'encodedFilter' => urlencode($filterPlugin->getHash()),
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
                'parentService' => $this->parentService
            ]
        );
    }

    public function noMemberAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $parentQuery  = $this->parentService
            ->findActiveParentWhichAreNoMember($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($parentQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilterForm($this->entityManager);
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisationService' => $this->organisationService,
                'contactService'      => $this->contactService,
                'parentService'       => $this->parentService,
            ]
        );
    }

    public function noMemberExportAction(): Response
    {
        $filterPlugin = $this->getOrganisationFilter();
        $parentQuery  = $this->parentService
            ->findActiveParentWhichAreNoMember($filterPlugin->getFilter());

        /** @var Entity\ParentEntity[] $parents */
        $parents = $parentQuery->getQuery()->getResult();

        // Open the output stream
        $fh = fopen('php://output', 'wb');


        ob_start();

        fputcsv(
            $fh,
            [
                'id',
                'name',
                'country',
                'iso3',
                'type',
                'member type',
                'artemisia type',
                'eposs type',
                'projects',
                'contact',
                'email',
                'street and number',
                'zip',
                'city',
                'country',
            ]
        );

        if (! empty($parents)) {
            foreach ($parents as $parent) {
                $projects = [];
                foreach ($parent->getParentOrganisation() as $parentOrganisation) {
                    foreach ($parentOrganisation->getAffiliation() as $affiliation) {
                        $projects[] = $affiliation->getProject()->parseFullName();
                    }
                }

                $address = $this->contactService->getMailAddress($parent->getContact());

                fputcsv(
                    $fh,
                    [
                        $parent->getId(),
                        $parent->getOrganisation()->getOrganisation(),
                        $parent->getOrganisation()->getCountry()->getCountry(),
                        $parent->getOrganisation()->getCountry()->getIso3(),
                        $parent->getType()->getType(),
                        $this->translator->translate($parent->getMemberType(true)),
                        $this->translator->translate($parent->getArtemisiaMemberType(true)),
                        $this->translator->translate($parent->getEpossMemberType(true)),
                        implode(';', $projects),
                        $parent->getContact()->parseFullName(),
                        $parent->getContact()->getEmail(),
                        null !== $address ? $address->getAddress() : '',
                        null !== $address ? $address->getZipCode() : '',
                        null !== $address ? $address->getCity() : '',
                        null !== $address ? $address->getCountry()->getCountry() : '',
                    ]
                );
            }
        }

        $string = ob_get_clean();

        // Convert to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');

        // Prepend BOM
        $string = '\xFF\xFE' . $string;

        /** @var Response $response */
        $response = $this->getResponse();
        /** @var Headers $headers */
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv');
        $headers->addHeaderLine(
            'Content-Disposition',
            'attachment; filename="export-members-with-are-no-member-and-have-no-doa.csv"'
        );
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($string));

        $response->setContent($string);

        return $response;
    }

    public function noFinancialAction(): ViewModel
    {
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getOrganisationFilter();
        $parentQuery  = $this->parentService
            ->findActiveParentWithoutFinancial($filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($parentQuery, false)));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new Form\ParentFilterForm();
        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel(
            [
                'paginator'           => $paginator,
                'form'                => $form,
                'encodedFilter'       => urlencode($filterPlugin->getHash()),
                'order'               => $filterPlugin->getOrder(),
                'direction'           => $filterPlugin->getDirection(),
                'organisationService' => $this->organisationService,
            ]
        );
    }
}
