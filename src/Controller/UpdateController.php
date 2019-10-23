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
use Organisation\Entity\Logo;
use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Entity\UpdateLogo;
use Organisation\Form\UpdateForm;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;
use function sprintf;

/**
 * Class UpdateController
 * @package Organisation\Controller
 */
class UpdateController extends OrganisationAbstractController
{
    /**
     * @var OrganisationService
     */
    private $organisationService;

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
        OrganisationService $organisationService,
        GeneralService      $generalservice,
        FormService         $formService,
        TranslatorInterface $translator
    )
    {
        $this->organisationService = $organisationService;
        $this->generalService      = $generalservice;
        $this->formService         = $formService;
        $this->translator          = $translator;
    }

    /**
     * @return ViewModel|Response
     */
    public function newAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        /** @var Organisation $organisation */
        $organisation = $this->organisationService->find(
            Organisation::class,
            (int) $this->params('organisationId', 0)
        );

        if ($organisation === null) {
            return $this->notFoundAction();
        }

        $update = new Update();
        $update->setDescription($organisation->getDescription()->getDescription());
        $update->setType($organisation->getType());
        $update->setOrganisation($organisation);

        $data = $request->getPost()->toArray();
        $form = $this->formService->prepare($update, $data);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community/contact/profile/organisation');
            }

            if ($form->isValid()) {
                /** @var Update $update */
                $update = $form->getData();
                $update->setContact($this->identity());

                $fileData = $this->params()->fromFiles();

                if (!empty($fileData['file']['name'])) {
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
                    $logo->setLogoExtension((string) $logo->getContentType()->getExtension());
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
