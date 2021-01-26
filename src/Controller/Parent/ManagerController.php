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
use DateTime;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;

/**
 * Class ParentController
 * @package Organisation\Controller
 */
final class ManagerController extends AbstractController
{
    private ParentService $parentService;
    private OrganisationService $organisationService;
    private ContactService $contactService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        ParentService $parentService,
        OrganisationService $organisationService,
        ContactService $contactService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->parentService       = $parentService;
        $this->organisationService = $organisationService;
        $this->contactService      = $contactService;
        $this->formService         = $formService;
        $this->translator          = $translator;
    }

    public function newAction()
    {
        $organisation = null;
        if (null !== $this->params('organisationId')) {
            $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));
        }

        $data = $this->getRequest()->getPost()->toArray();

        $parent = new Entity\ParentEntity();
        $form   = $this->formService->prepare($parent, $data);
        $form->remove('delete');

        if (null !== $organisation) {
            //Inject the organisation in the form
            $form->get($parent->get('underscore_entity_name'))->get('organisation')
                ->setValueOptions([$organisation->getId() => $organisation->getOrganisation()]);

            $contactsInOrganisation = [];
            foreach ($this->contactService->findContactsInOrganisation($organisation) as $contact) {
                $contactsInOrganisation[$contact->getId()] = $contact->getFormName();
            }
            asort($contactsInOrganisation);

            //Inject the organisation in the form
            $form->get($parent->get('underscore_entity_name'))->get('contact')
                ->setValueOptions($contactsInOrganisation);
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/parent/list/parent');
            }

            if ($form->isValid()) {
                /* @var $parent Entity\ParentEntity */
                $parent = $form->getData();

                $parent->setDateParentTypeUpdate(new DateTime());

                $this->parentService->save($parent);
                $this->organisationService->save($parent->getOrganisation());

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/general',
                    [
                        'id' => $parent->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $currentParentType = $parent->getType()->getId();

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($parent, $data);

        $form->get($parent->get('underscore_entity_name'))->get('contact')->injectContact($parent->getContact());
        $form->get($parent->get('underscore_entity_name'))->get('organisation')
            ->injectOrganisation($parent->getOrganisation());

        if (! $this->parentService->canDeleteParent($parent)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/parent/list/parent');
            }

            if (isset($data['delete']) && $this->parentService->canDeleteParent($parent)) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-parent-%s-has-successfully-been-deleted'), $parent)
                );
                /** @var Entity\Organisation $organisation */
                $organisation = $parent->getOrganisation();
                $organisation->setParent(null);

                $this->parentService->delete($parent);

                $this->organisationService->save($organisation);

                return $this->redirect()->toRoute('zfcadmin/parent/list/parent');
            }

            if ($form->isValid()) {
                /* @var $parent Entity\ParentEntity */
                $parent = $form->getData();

                if ($parent->getType()->getId() !== $currentParentType) {
                    $parent->setDateParentTypeUpdate(new DateTime());
                }

                $this->flashMessenger()->addSuccessMessage(
                    sprintf($this->translator->translate('txt-parent-%s-has-successfully-been-updated'), $parent)
                );

                $parent = $this->parentService->save($parent);

                $this->organisationService->save($parent->getOrganisation());

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/general',
                    [
                        'id' => $parent->getId(),
                    ]
                );
            }
        }

        return new ViewModel(
            [
                'form'   => $form,
                'parent' => $parent,
            ]
        );
    }

    public function createParentOrganisationAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('id'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $organisation = null;
        if (null !== $this->params('organisationId')) {
            $organisation = $this->organisationService->findOrganisationById((int)$this->params('organisationId'));
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\Parent\CreateParentOrganisationForm();

        if (null !== $organisation) {
            //Inject the organisation in the form
            $form->get('organisation')->setValueOptions([$organisation->getId() => $organisation->getOrganisation()]);

            $contactsInOrganisation = [];
            foreach ($this->contactService->findContactsInOrganisation($organisation) as $contact) {
                $contactsInOrganisation[$contact->getId()] = $contact->getFormName();
            }
            asort($contactsInOrganisation);

            //Inject the organisation in the form
            $form->get('contact')->setValueOptions($contactsInOrganisation);
        }

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/details/general',
                    [
                        'id' => $parent->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $parentOrganisation = new Entity\Parent\Organisation();
                $parentOrganisation->setParent($parent);

                //Find the organisation from the form
                $organisation = $this->organisationService->findOrganisationById((int)$data['organisation']);
                $parentOrganisation->setOrganisation($organisation);

                //Find the contact from the form.
                $contact = $this->contactService->findContactById((int)$data['contact']);
                $parentOrganisation->setContact($contact);


                $parentOrganisation = $this->parentService->save($parentOrganisation);

                $this->organisationService->save($organisation);

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/organisation/view',
                    [
                        'id' => $parentOrganisation->getId(),
                    ]
                );
            }
        }

        return new ViewModel(
            [
                'form'   => $form,
                'parent' => $parent,
            ]
        );
    }
}
