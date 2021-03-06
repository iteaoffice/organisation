<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Controller;

use General\Service\GeneralService;
use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\MimeType;
use Laminas\View\Model\ViewModel;
use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Entity\UpdateLogo;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;

/**
 * Class UpdateController
 * @package Organisation\Controller
 */
final class UpdateController extends AbstractController
{
    private OrganisationService $organisationService;
    private GeneralService $generalService;
    private FormService $formService;
    private TranslatorInterface $translator;

    public function __construct(
        OrganisationService $organisationService,
        GeneralService $generalService,
        FormService $formService,
        TranslatorInterface $translator
    ) {
        $this->organisationService = $organisationService;
        $this->generalService      = $generalService;
        $this->formService         = $formService;
        $this->translator          = $translator;
    }

    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Organisation $organisation */
        $organisation = $this->organisationService->find(
            Organisation::class,
            (int)$this->params('organisationId', 0)
        );

        if ($organisation === null) {
            return $this->notFoundAction();
        }

        $update = new Update();

        if (null !== $organisation->getDescription()) {
            $update->setDescription($organisation->getDescription()->getDescription());
        }
        $update->setType($organisation->getType());
        $update->setOrganisation($organisation);
        if (null !== $this->organisationService->findMainWeb($organisation)) {
            $update->setWebsite($this->organisationService->findMainWeb($organisation)->getWeb());
        }

        $data = array_merge(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray(),
        );

        $form = $this->formService->prepare($update, $data);
        $form->remove('delete');

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community/contact/profile/organisation');
            }

            if ($form->isValid()) {
                /** @var Update $update */
                $update = $form->getData();
                $update->setContact($this->identity());

                $fileData = $this->params()->fromFiles();

                if (! empty($fileData['file']['name'])) {
                    $logo = new UpdateLogo();
                    $logo->setUpdate($update);
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

                $this->organisationService->save($update);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-organisation-update-successfully-received')
                );

                return $this->redirect()->toRoute('community/contact/profile/organisation');
            }
        }

        return new ViewModel([
            'update' => $update,
            'form'   => $form
        ]);
    }
}
