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

use Organisation\Entity\Note;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Zend\Http\Request;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class NoteController
 *
 * @package Organisation\Controller
 */
final class NoteController extends OrganisationAbstractController
{
    /**
     * @var OrganisationService
     */
    private $organisationService;
    /**
     * @var FormService
     */
    private $formService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        OrganisationService $organisationService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->formService = $formService;
        $this->translator = $translator;
    }


    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $note = new Note();
        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($note, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'note']
                );
            }

            if ($form->isValid()) {
                /** @var Note $note */
                $note = $form->getData();
                $note->setOrganisation($organisation);
                $note->setContact($this->identity());
                $this->organisationService->save($note);
                $this->flashMessenger()->addSuccessMessage(
                    \sprintf(
                        $this->translator->translate('txt-note-for-organisation-%s-has-successfully-been-added'),
                        $organisation
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'note']
                );
            }
        }

        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }

    public function editAction()
    {
        /** @var Note $note */
        $note = $this->organisationService->find(Note::class, (int) $this->params('id'));
        /** @var Request $request */
        $request = $this->getRequest();

        if (null === $note) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($note, $data);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $note->getOrganisation()->getId()],
                    ['fragment' => 'note']
                );
            }

            if (isset($data['delete'])) {
                $this->organisationService->delete($note);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    $this->translator->translate("txt-note-has-been-removed-successfully")
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $note->getOrganisation()->getId()],
                    ['fragment' => 'note']
                );
            }

            if ($form->isValid()) {
                /** @var Note $note */
                $note = $form->getData();
                $this->organisationService->save($note);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    $this->translator->translate("txt-note-has-successfully-been-updated")
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $note->getOrganisation()->getId()],
                    ['fragment' => 'note']
                );
            }
        }

        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }
}
