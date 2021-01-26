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
use Invoice\Service\InvoiceService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Program\Service\ProgramService;

/**
 * Class ParentController
 * @package Organisation\Controller
 */
final class DetailsController extends AbstractController
{
    private ParentService $parentService;
    private OrganisationService $organisationService;
    private ContactService $contactService;
    private ProgramService $programService;
    private InvoiceService $invoiceService;
    private EntityManager $entityManager;
    private TranslatorInterface $translator;

    public function __construct(
        ParentService $parentService,
        OrganisationService $organisationService,
        ContactService $contactService,
        ProgramService $programService,
        InvoiceService $invoiceService,
        EntityManager $entityManager,
        TranslatorInterface $translator
    ) {
        $this->parentService       = $parentService;
        $this->organisationService = $organisationService;
        $this->contactService      = $contactService;
        $this->programService      = $programService;
        $this->invoiceService      = $invoiceService;
        $this->entityManager       = $entityManager;
        $this->translator          = $translator;
    }

    public function viewAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $year = (int)date('Y');

        $form = new Form\CreateParentDoa($this->entityManager);
        $form->setData($this->getRequest()->getPost()->toArray());
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $counter = 0;
            foreach ((array)$form->getData()['program'] as $programId) {
                $program = $this->programService->findProgramById((int)$programId);
                if (null !== $program) {
                    $doa = new Entity\Parent\Doa();
                    $doa->setContact($parent->getContact());
                    $doa->setParent($parent);
                    $doa->setProgram($program);
                    $this->parentService->save($doa);
                    $counter++;
                }
            }

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate('txt-%s-parent-doa-have-been-created-for-%s'),
                    $counter,
                    $parent
                )
            );

            return $this->redirect()->toRoute(
                'zfcadmin/parent/details/doas',
                [
                    'id' => $parent->getId(),
                ],
            );
        }

        return new ViewModel(
            [
                'parent'              => $parent,
                'organisationService' => $this->organisationService,
                'contactService'      => $this->contactService,
                'year'                => $year,
                'form'                => $form,
                'programs'            => $this->programService->findAll(Program::class),
                'parentService'       => $this->parentService,
                'invoiceService'      => $this->invoiceService
            ]
        );
    }
}
