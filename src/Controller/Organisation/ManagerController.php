<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller\Organisation;

use Affiliation\Entity\Affiliation;
use Affiliation\Service\AffiliationService;
use Contact\Service\ContactService;
use General\Service\GeneralService;
use Laminas\Form\Fieldset;
use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Entity\Web;
use Organisation\Form;
use Organisation\InputFilter;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Project\Service\ProjectService;

use function sprintf;

/**
 * Class ManagerController
 * @package Organisation\Controller\Admin
 */
class ManagerController extends AbstractController
{
    private OrganisationService $organisationService;
    private ProjectService $projectService;
    private ContactService $contactService;
    private AffiliationService $affiliationService;
    private GeneralService $generalService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        OrganisationService $organisationService,
        ProjectService $projectService,
        ContactService $contactService,
        AffiliationService $affiliationService,
        GeneralService $generalService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->projectService      = $projectService;
        $this->contactService      = $contactService;
        $this->affiliationService  = $affiliationService;
        $this->generalService      = $generalService;
        $this->formService         = $formService;
        $this->translator          = $translator;
    }

    public function newAction()
    {
        $organisation = new Organisation();
        /** @var Request $request */
        $request = $this->getRequest();
        $data    = array_merge($request->getPost()->toArray(), $request->getFiles()->toArray());
        $form    = $this->formService->prepare($organisation, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/list/organisation');
            }

            if ($form->isValid()) {
                /** @var Organisation $organisation */
                $organisation = $form->getData();
                $organisation->getDescription()->setOrganisation($organisation);
                // Ignore empty description
                if (empty($organisation->getDescription()->getDescription())) {
                    $organisation->setDescription(null);
                }

                $fileData = $this->params()->fromFiles();

                if (! empty($fileData['file']['name'])) {
                    $logo = new Logo();
                    $logo->setOrganisation($organisation);
                    $logo->setOrganisationLogo(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $logo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                    $logo->setLogoExtension($logo->getContentType()->getExtension());
                    $organisation->getLogo()->add($logo);
                }

                $this->organisationService->save($organisation);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-successfully-been-added'),
                        $organisation
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/details/general', ['id' => $organisation->getId()]);
            }
        }

        return new ViewModel(
            [
                'form'         => $form,
                'organisation' => $organisation
            ]
        );
    }

    public function editAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($organisation, $data);

        if (! $this->organisationService->canDeleteOrganisation($organisation)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/details/general', ['id' => $organisation->getId()]);
            }

            if (isset($data['delete']) && $this->organisationService->canDeleteOrganisation($organisation)) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-been-removed-successfully'),
                        $organisation
                    )
                );

                $this->organisationService->delete($organisation);

                return $this->redirect()->toRoute('zfcadmin/organisation/list/organisation');
            }

            if ($form->isValid()) {
                /** @var Organisation $organisation */
                $organisation = $form->getData();
                $organisation->getDescription()->setOrganisation($organisation);
                // Remove an empty description
                if (empty($organisation->getDescription()->getDescription())) {
                    $this->organisationService->delete($organisation->getDescription());
                    $organisation->setDescription(null);
                }

                $fileData = $this->params()->fromFiles();

                if (! empty($fileData['file']['name'])) {
                    $logo = $organisation->getLogo()->first();
                    if (! $logo) {
                        // Create a new logo element
                        $logo = new Logo();
                        $logo->setOrganisation($organisation);
                    }
                    $logo->setOrganisationLogo(file_get_contents($fileData['file']['tmp_name']));
                    $imageSizeValidator = new ImageSize();
                    $imageSizeValidator->isValid($fileData['file']);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $logo->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                    $logo->setLogoExtension((string)$logo->getContentType()->getExtension());
                    $organisation->getLogo()->add($logo);
                }

                $this->organisationService->save($organisation);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-organisation-%s-has-successfully-been-updated'),
                        $organisation
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/organisation/details/general', ['id' => $organisation->getId()]);
            }
        }

        return new ViewModel(
            [
                'organisation' => $organisation,
                'form'         => $form,
            ]
        );
    }

    public function webAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $form = new Form\Organisation\ManageWebForm($organisation);
        //Prepare an array for population
        $population = [];
        foreach ($organisation->getWeb() as $web) {
            $population['webFieldset'][$web->getId()] = ['delete' => ''];

            /** @var Fieldset $webFieldset */
            $webFieldset = $form->get('webFieldset');

            //inject the existing webs in the array
            foreach ($webFieldset as $webId => $webElement) {
                if ($webId === $web->getId()) {
                    $webElement->get('web')->setValue($web->getWeb());
                    $webElement->get('main')->setValue((int)$web->getMain());
                }
            }
        }

        $data = ArrayUtils::merge($population, $this->getRequest()->getPost()->toArray(), true);


        $form->setInputFilter(new InputFilter\ManageWebFilter($organisation));
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/organisation/details/general', ['id' => $this->params('id')]);
            }

            if ($form->isValid()) {
                $data = $form->getData();

                if (isset($data['webFieldset']) && is_array($data['webFieldset'])) {
                    foreach ($data['webFieldset'] as $webId => $information) {
                        /**
                         * //Find the corresponding web
                         *
                         * @var $web Web
                         */
                        $web = $this->organisationService->find(Web::class, (int)$webId);

                        if (isset($information['delete']) && $information['delete'] === '1') {
                            $this->organisationService->delete($web);
                        } else {
                            $web->setOrganisation($organisation);
                            $web->setWeb($information['web']);
                            $web->setMain((int)$information['main']);
                            $this->organisationService->save($web);
                        }
                    }
                }

                //Handle the new web (if provided)
                if (! empty($data['web'])) {
                    $web = new Web();
                    $web->setOrganisation($organisation);
                    $web->setWeb($data['web']);
                    $web->setMain((int)$data['main']);

                    $this->organisationService->save($web);
                }

                if (isset($data['submit'])) {
                    return $this->redirect()->toRoute('zfcadmin/organisation/details/general', ['id' => $this->params('id')]);
                }

                return $this->redirect()->toRoute('zfcadmin/organisation/web', ['id' => $this->params('id')]);
            }
        }

        return new ViewModel(
            [
                'organisationService' => $this->organisationService,
                'organisation'        => $organisation,
                'form'                => $form,
            ]
        );
    }

    public function createAffiliationAction()
    {
        $organisation = $this->organisationService->findOrganisationById((int)$this->params('id'));

        if (null === $organisation) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\Organisation\CreateAffiliationForm($this->projectService, $organisation);
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/details/projects',
                    ['id' => $organisation->getId()]
                );
            }

            if ($form->isValid()) {
                $formData = $form->getData();

                $project = $this->projectService->findProjectById((int)$formData['project']);
                $contact = $this->contactService->findContactById((int)$formData['contact']);
                $branch  = $formData['branch'];

                $affiliation = new Affiliation();
                $affiliation->setProject($project);
                $affiliation->setOrganisation($organisation);
                if (! empty($branch)) {
                    $affiliation->setBranch($branch);
                }
                $affiliation->setContact($contact);

                $this->affiliationService->save($affiliation);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate(
                            'txt-organisation-%s-has-successfully-been-added-to-project-%s'
                        ),
                        $organisation,
                        $project
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/details/projects',
                    ['id' => $organisation->getId()],
                );
            }
        }


        return new ViewModel(
            [
                'organisation' => $organisation,
                'form'         => $form,
            ]
        );
    }

    public function mergeAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Organisation $source */
        $source = $this->organisationService->findOrganisationById((int)$this->params('sourceId'));
        /** @var Organisation $target */
        $target = $this->organisationService->findOrganisationById((int)$this->params('targetId'));

        if (null === $source || null === $target) {
            return $this->notFoundAction();
        }

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            // Cancel the merge
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/details/merge',
                    ['id' => $target->getId()]
                );
            }

            // Swap source and destination
            if (isset($data['swap'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/merge',
                    ['sourceId' => $target->getId(), 'targetId' => $source->getId()]
                );
            }

            // Do the merge
            if (isset($data['merge'])) {
                $result = $this->organisationMerge()->merge($source, $target);
                $route  = 'zfcadmin/organisation/details/general';
                if ($result['success']) {
                    $this->flashMessenger()->addSuccessMessage(
                        $this->translator->translate('txt-organisations-have-been-successfully-merged')
                    );
                } else {
                    $route = 'zfcadmin/organisation/details/merge';
                    $this->flashMessenger()->setNamespace('error')->addMessage(
                        $this->translator->translate('txt-organisation-merge-failed')
                    );
                }

                return $this->redirect()->toRoute(
                    $route,
                    ['id' => $target->getId()]
                );
            }
        }

        return new ViewModel(
            [
                'errors'              => $this->organisationMerge()->checkMerge($source, $target),
                'source'              => $source,
                'target'              => $target,
                'mergeForm'           => new Form\Organisation\MergeForm(),
                'organisationService' => $this->organisationService,
            ]
        );
    }
}
