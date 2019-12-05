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

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\ParentService;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Zend\Http\Request;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * Class ParentOrganisationController
 *
 * @package Organisation\Controller
 */
final class ParentOrganisationController extends OrganisationAbstractController
{
    private ParentService $parentService;
    private ProjectService $projectService;
    private AffiliationService $affiliationService;
    private ContactService $contactService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        ParentService $parentService,
        ProjectService $projectService,
        AffiliationService $affiliationService,
        ContactService $contactService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->parentService = $parentService;
        $this->projectService = $projectService;
        $this->affiliationService = $affiliationService;
        $this->contactService = $contactService;
        $this->formService = $formService;
        $this->translator = $translator;
    }


    public function editAction()
    {
        /** @var Entity\Parent\Organisation $organisation */
        $organisation = $this->parentService
            ->find(Entity\Parent\Organisation::class, (int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($organisation, $data);
        $form->get($organisation->get('underscore_entity_name'))->get('parent')
            ->injectParent($organisation->getParent());
        $form->get($organisation->get('underscore_entity_name'))->get('contact')
            ->injectContact($organisation->getContact());
        $form->get($organisation->get('underscore_entity_name'))->get('organisation')
            ->injectOrganisation($organisation->getOrganisation());

        if (!$this->parentService->canDeleteParentOrganisation($organisation)) {
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

            if (isset($data['delete']) && $this->parentService->canDeleteParentOrganisation($organisation)) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-organisation-%s-has-been-removed-from-the-parent-successfully'
                        ),
                        $organisation
                    )
                );

                $this->parentService->delete($organisation);

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

                $organisation = $this->parentService->save($organisation);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-been-updated-successfully'),
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


    public function viewAction(): ViewModel
    {
        /** @var Entity\Parent\Organisation $parentOrganisation */
        $parentOrganisation = $this->parentService->find(Entity\Parent\Organisation::class, (int)$this->params('id'));

        if (null === $parentOrganisation) {
            return $this->notFoundAction();
        }

        return new ViewModel(['parentOrganisation' => $parentOrganisation]);
    }

    public function mergeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        /** @var Entity\Parent\Organisation $organisation */
        $organisation = $this->parentService->find(Entity\Parent\Organisation::class, (int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $request->getPost()->toArray();

        if (isset($data['merge'], $data['submit']) && $request->isPost()) {

            /** @var Entity\Parent\Organisation $otherOrganisation */
            $otherOrganisation = $this->parentService
                ->find(Entity\Parent\Organisation::class, (int)$data['merge']);

            $result = $this->mergeParentOrganisation($organisation, $otherOrganisation);

            if ($result['success'] === true) {
                $this->flashMessenger()->setNamespace(FlashMessenger::NAMESPACE_SUCCESS)
                    ->addMessage(
                        sprintf(
                            $this->translator->translate(
                                'txt-merge-of-organisation-%s-and-%s-in-in-parent-%s-was-successful'
                            ),
                            $organisation->getOrganisation(),
                            $otherOrganisation->getOrganisation(),
                            $organisation->getParent()->getOrganisation()
                        )
                    );
            } else {
                $this->flashMessenger()->setNamespace(FlashMessenger::NAMESPACE_ERROR)
                    ->addMessage(
                        sprintf($this->translator->translate('txt-merge-failed:-%s'), $result['errorMessage'])
                    );
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
                'parentService' => $this->parentService
            ]
        );
    }

    public function addAffiliationAction()
    {
        /** @var Entity\Parent\Organisation $parentOrganisation */
        $parentOrganisation = $this->parentService
            ->find(Entity\Parent\Organisation::class, (int)$this->params('id'));

        if (null === $parentOrganisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\AddParentAffiliation($this->projectService, $parentOrganisation);
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
                $project = $this->projectService->findProjectById((int)$formData['project']);
                /** @var Contact $contact */
                $contact = $this->contactService->findContactById((int)$formData['contact']);
                $branch = $formData['branch'];

                $affiliation = new Affiliation();
                $affiliation->setProject($project);
                $affiliation->setOrganisation($parentOrganisation->getOrganisation());
                $affiliation->setParentOrganisation($parentOrganisation);
                if (!empty($branch)) {
                    $affiliation->setBranch($branch);
                }
                $affiliation->setContact($contact);

                $this->affiliationService->save($affiliation);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-organisation-%s-has-successfully-been-added-to-project-%s'
                        ),
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
