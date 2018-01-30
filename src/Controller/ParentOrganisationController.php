<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Affiliation\Entity\Affiliation;
use Contact\Entity\Contact;
use Organisation\Entity;
use Organisation\Form;
use Project\Entity\Project;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * @category    Parent
 */
class ParentOrganisationController extends OrganisationAbstractController
{

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /** @var Entity\Parent\Organisation $organisation */
        $organisation = $this->getParentService()
            ->findEntityById(Entity\Parent\Organisation::class, $this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->getFormService()->prepare($organisation, $organisation, $data);
        $form->get($organisation->get('underscore_entity_name'))->get('contact')
            ->injectContact($organisation->getContact());
        $form->get($organisation->get('underscore_entity_name'))->get('organisation')
            ->injectOrganisation($organisation->getOrganisation());

        if (!$this->getParentService()->canDeleteParentOrganisation($organisation)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/organisation/view',
                    [
                        'id' => $organisation->getId(),
                    ]
                );
            }

            if (isset($data['delete']) && $this->getParentService()->canDeleteParentOrganisation($organisation)) {
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-organisation-%s-has-been-removed-from-the-parent-successfully"),
                            $organisation
                        )
                    );

                $this->getParentService()->removeEntity($organisation);

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    [
                        'id' => $organisation->getParent()->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /* @var  Entity\Parent\Organisation $organisation */
                $organisation = $form->getData();

                $organisation = $this->getParentService()->newEntity($organisation);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-organisation-%s-has-been-updated-successfully"),
                            $organisation
                        )
                    );


                return $this->redirect()->toRoute(
                    'zfcadmin/parent/organisation/view',
                    [
                        'id' => $organisation->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * @return ViewModel
     */
    public function viewAction(): ViewModel
    {
        /** @var Entity\Parent\Organisation $organisation */
        $organisation = $this->getParentService()
            ->findEntityById(Entity\Parent\Organisation::class, $this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        return new ViewModel(['organisation' => $organisation]);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function mergeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        /** @var Entity\Parent\Organisation $organisation */
        $organisation = $this->getParentService()
            ->findEntityById(Entity\Parent\Organisation::class, $this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();

        if (isset($data['merge'], $data['submit']) && $request->isPost()) {

            /** @var Entity\Parent\Organisation $otherOrganisation */
            $otherOrganisation = $this->getParentService()
                ->findEntityById(Entity\Parent\Organisation::class, (int)$data['merge']);

            $result = $this->mergeParentOrganisation($organisation, $otherOrganisation);

            if ($result['success'] === true) {
                $this->flashMessenger()->setNamespace(FlashMessenger::NAMESPACE_SUCCESS)
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-merge-of-organisation-%s-and-%s-in-in-parent-%s-was-successful"),
                            $organisation->getOrganisation(),
                            $otherOrganisation->getOrganisation(),
                            $organisation->getParent()->getOrganisation()
                        )
                    );
            } else {
                $this->flashMessenger()->setNamespace(FlashMessenger::NAMESPACE_ERROR)
                    ->addMessage(sprintf($this->translate('txt-merge-failed:-%s'), $result['errorMessage']));
            }

            return $this->redirect()->toRoute(
                'zfcadmin/parent/view',
                ['id' => $organisation->getParent()->getId()]
            );
        }

        return new ViewModel(
            [
                'organisation'  => $organisation,
                'merge'         => $data['merge'] ?? null,
                'parentService' => $this->getParentService()
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAffiliationAction()
    {
        /** @var Entity\Parent\Organisation $parentOrganisation */
        $parentOrganisation = $this->getParentService()
            ->findEntityById(Entity\Parent\Organisation::class, $this->params('id'));

        if (\is_null($parentOrganisation)) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\AddAffiliation($this->getProjectService(), $parentOrganisation);
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(
                        'zfcadmin/parent/organisation/view',
                        ['id' => $parentOrganisation->getId()],
                        ['fragment' => 'project']
                    );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                /** @var Project $project */
                $project = $this->getProjectService()->findProjectById((int)$formData['project']);
                /** @var Contact $contact */
                $contact = $this->getContactService()->findContactById((int)$formData['contact']);
                $branch = $formData['branch'];

                $affiliation = new Affiliation();
                $affiliation->setProject($project);
                $affiliation->setOrganisation($parentOrganisation->getOrganisation());
                $affiliation->setParentOrganisation($parentOrganisation);
                if (!empty($branch)) {
                    $affiliation->setBranch($branch);
                }
                $affiliation->setContact($contact);

                $this->getAffiliationService()->newEntity($affiliation);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-organisation-%s-has-successfully-been-added-to-project-%s"),
                            $parentOrganisation,
                            $project
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/affiliation/view',
                    ['id' => $affiliation->getId()],
                    []
                );
            }
        }


        return new ViewModel(
            [
                'organisation' => $parentOrganisation,
                'form'         => $form,
            ]
        );
    }
}
