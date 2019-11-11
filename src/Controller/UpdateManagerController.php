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

use General\Service\GeneralService;
use Organisation\Entity\Update;
use Organisation\Entity\UpdateLogo;
use Organisation\Service\FormService;
use Organisation\Service\UpdateService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class UpdateManagerController
 *
 * @package Organisation\Controller
 */
class UpdateManagerController extends OrganisationAbstractController
{
    /**
     * @var UpdateService
     */
    private $updateService;

    /**
     * @var GeneralService
     */
    private $generalService;

    /**
     * @var FormService
     */
    private $formService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UpdateService $updateService,
        GeneralService $generalService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->updateService = $updateService;
        $this->generalService = $generalService;
        $this->formService = $formService;
        $this->translator = $translator;
    }

    public function pendingAction(): ViewModel
    {
        return new ViewModel(
            [
                'pendingUpdates' => $this->updateService->findPendingUpdates()
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        /** @var Update $update */
        $update = $this->updateService->find(Update::class, (int)$this->params('id'));

        if (null === $update) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'update' => $update
            ]
        );
    }

    public function editAction()
    {
        /** @var Update $update */
        $update = $this->updateService->find(Update::class, (int)$this->params('id'));
        /** @var Request $request */
        $request = $this->getRequest();

        if (null === $update) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray(),
        );
        $form = $this->formService->prepare($update, $data);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/update/view',
                    ['id' => $update->getId()]
                );
            }

            if (isset($data['delete'])) {
                $this->updateService->delete($update);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-update-has-been-removed-successfully')
                );
                return $this->redirect()->toRoute('zfcadmin/organisation/update/pending');
            }

            if ($form->isValid()) {
                /** @var Update $update */
                $update = $form->getData();

                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['file']['name'])) {
                    $logo = $update->getLogo();
                    if ($logo === null) {
                        $logo = new UpdateLogo();
                        $logo->setUpdate($update);
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
                    $update->setLogo($logo);
                }

                $this->updateService->save($update);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-update-has-successfully-been-modified')
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/organisation/update/view',
                    ['id' => $update->getId()]
                );
            }
        }

        return new ViewModel(
            [
                'update' => $update,
                'form'   => $form,
            ]
        );
    }

    /**
     * @return ViewModel|Response
     */
    public function approveAction()
    {
        /** @var Update $update */
        $update = $this->updateService->find(Update::class, (int)$this->params('id', 0));

        if ($update === null) {
            return $this->notFoundAction();
        }

        $this->updateService->approveUpdate($update);

        $this->flashMessenger()->addSuccessMessage(
            $this->translator->translate('txt-organisation-update-successfully-applied')
        );

        return $this->redirect()->toRoute('zfcadmin/organisation/update/list');
    }
}
