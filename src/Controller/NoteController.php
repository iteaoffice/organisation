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
use Zend\Http\Request;
use Zend\View\Model\ViewModel;

/**
 * Class NoteController
 * @package Organisation\Controller
 */
class NoteController extends OrganisationAbstractController
{
    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $organisation = $this->getOrganisationService()->findOrganisationById($this->params('organisationId'));
        $note = new Note();
        $data = $request->getPost()->toArray();
        $form = $this->getFormService()->prepare($note, $note, $data);
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
                $note->setContact($this->zfcUserAuthentication()->getIdentity());
                $this->getOrganisationService()->updateEntity($note);
                $this->flashMessenger()->setNamespace('success')->addMessage(sprintf(
                    $this->translate("txt-note-for-organisation-%s-has-successfully-been-added"),
                    $organisation
                ));

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $organisation->getId()],
                    ['fragment' => 'note']
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /** @var Note $note */
        $note = $this->getOrganisationService()->findEntityById(Note::class, $this->params('id'));
        /** @var Request $request */
        $request = $this->getRequest();

        if (\is_null($note)) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();
        $form = $this->getFormService()->prepare($note, $note, $data);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $note->getOrganisation()->getId()],
                    ['fragment' => 'note']
                );
            }

            if (isset($data['delete'])) {
                $this->getOrganisationService()->removeEntity($note);
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    $this->translate("txt-note-has-been-removed-successfully")
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
                $this->getOrganisationService()->updateEntity($note);

                $this->flashMessenger()->setNamespace('success')->addMessage(
                    $this->translate("txt-note-has-successfully-been-updated")
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/view',
                    ['id' => $note->getOrganisation()->getId()],
                    ['fragment' => 'note']
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }
}
