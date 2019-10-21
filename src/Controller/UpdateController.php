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

use Organisation\Entity\Organisation;
use Organisation\Entity\Update;
use Organisation\Form\UpdateForm;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
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
     * @var FormService
     */
    private $formService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        OrganisationService $organisationService,
        FormService         $formService,
        TranslatorInterface $translator
    )
    {
        $this->organisationService = $organisationService;
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
        $form = $this->formService->prepare($update);

        if ($request->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('community/contact/profile/organisation');
            }

            if ($form->isValid()) {
                /** @var Update $update */
                $update = $form->getData();
                $update->setOrganisation($organisation);
                $update->setContact($this->identity());
                $this->organisationService->save($update);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-organisation-update-successfully-received')
                );

                return $this->redirect()->toRoute('community/contact/profile/organisation');
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }
}
