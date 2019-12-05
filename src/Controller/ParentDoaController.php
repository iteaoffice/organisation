<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Controller;

use Contact\Service\ContactService;
use DateTime;
use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Organisation\Entity;
use Organisation\Form\ParentDoa;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Program\Service\ProgramService;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;
use function file_get_contents;

/**
 * Class ParentDoaController
 *
 * @package Organisation\Controller
 */
final class ParentDoaController extends OrganisationAbstractController
{
    private ParentService $parentService;
    private EntityManager $entityManager;
    private GeneralService $generalService;
    private ContactService $contactService;
    private ProgramService $programService;
    private TranslatorInterface $translator;

    public function __construct(
        ParentService $parentService,
        EntityManager $entityManager,
        GeneralService $generalService,
        ContactService $contactService,
        ProgramService $programService,
        TranslatorInterface $translator
    ) {
        $this->parentService = $parentService;
        $this->entityManager = $entityManager;
        $this->generalService = $generalService;
        $this->contactService = $contactService;
        $this->programService = $programService;
        $this->translator = $translator;
    }


    public function uploadAction()
    {
        $parent = $this->parentService->findParentById((int)$this->params('parentId'));

        if (null === $parent) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new ParentDoa($this->entityManager);
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $parent->getId()]
                );
            }

            if ($form->isValid()) {
                $fileData = $this->params()->fromFiles();
                //Create a article object element
                $doaObject = new Entity\Parent\DoaObject();
                $doaObject->setObject(file_get_contents($fileData['file']['tmp_name']));
                $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                $fileSizeValidator->isValid($fileData['file']);
                $doa = new Entity\Parent\Doa();
                $doa->setSize($fileSizeValidator->size);

                $fileTypeValidator = new MimeType();
                $fileTypeValidator->isValid($fileData['file']);
                $doa->setContentType($this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type));

                $doa->setContact($this->contactService->findContactById((int)$data['contact']));
                if ($dateSigned = DateTime::createFromFormat('Y-m-d', $data['dateSigned'])) {
                    $doa->setDateSigned($dateSigned);
                }
                if ($dateApproved = DateTime::createFromFormat('Y-m-d', $data['dateApproved'])) {
                    $doa->setDateApproved($dateApproved);
                }

                //FInd the program
                /** @var Program $program */
                $program = $this->programService->findProgramById((int)$data['program']);
                $doa->setProgram($program);

                $doa->setParent($parent);
                $doaObject->setDoa($doa);
                $this->parentService->save($doaObject);
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-doa-for-parent-%s-has-been-uploaded'),
                        $parent->getOrganisation()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $parent->getId()]
                );
            }
        }

        return new ViewModel(
            [
                'parent' => $parent,
                'form'   => $form,
            ]
        );
    }

    public function editAction()
    {
        /** @var Entity\Parent\Doa $doa */
        $doa = $this->parentService->find(Entity\Parent\Doa::class, (int)$this->params('id'));

        if (null === $doa) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'dateSigned'   => null === $doa->getDateSigned() ? null : $doa->getDateSigned()->format('Y-m-d'),
                'dateApproved' => null === $doa->getDateApproved() ? null : $doa->getDateApproved()->format('Y-m-d'),
            ],
            $this->getRequest()->getFiles()->toArray(),
            $this->getRequest()->getPost()->toArray()
        );
        $form = new ParentDoa($this->entityManager);
        $form->setData($data);

        $form->get('contact')->injectContact($doa->getContact());
        $form->getInputFilter()->get('file')->setRequired(false);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $doa->getParent()->getId()]
                );
            }

            if (isset($data['delete'])) {
                $this->parentService->delete($doa);

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $doa->getParent()->getId()]
                );
            }

            if ($form->isValid()) {
                $fileData = $this->params()->fromFiles();

                if ($fileData['file']['error'] === 0) {
                    /*
                     * Replace the content of the object
                     */
                    if (!$doa->getObject()->isEmpty()) {
                        $doa->getObject()->first()->setObject(
                            file_get_contents($fileData['file']['tmp_name'])
                        );
                    } else {
                        $doaObject = new Entity\Parent\DoaObject();
                        $doaObject->setObject(
                            file_get_contents($fileData['file']['tmp_name'])
                        );
                        $doaObject->setDoa($doa);
                        $this->parentService->save($doaObject);
                    }

                    //Create a article object element
                    $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                    $fileSizeValidator->isValid($fileData['file']);
                    $doa->setSize($fileSizeValidator->size);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $doa->setContentType(
                        $this->generalService->findContentTypeByContentTypeName($fileTypeValidator->type)
                    );
                }


                $doa->setContact($this->contactService->findContactById((int)$data['contact']));
                if ($dateSigned = DateTime::createFromFormat('Y-m-d', $data['dateSigned'])) {
                    $doa->setDateSigned($dateSigned);
                }
                if ($dateApproved = DateTime::createFromFormat('Y-m-d', $data['dateApproved'])) {
                    $doa->setDateApproved($dateApproved);
                }

                $this->parentService->save($doa);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-doa-for-parent-%s-has-been-uploaded'),
                        $doa->getParent()->getOrganisation()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $doa->getParent()->getId()]
                );
            }
        }

        return new ViewModel(
            [
                'doa'  => $doa,
                'form' => $form,
            ]
        );
    }

    public function downloadAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /**
         * @var Entity\Parent\Doa $doa
         */
        $doa = $this->parentService->find(Entity\Parent\Doa::class, (int)$this->params('id'));

        if (null === $doa || count($doa->getObject()) === 0) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $doa->getObject()->first()->getObject();

        $response->setContent(stream_get_contents($object));
        $response->getHeaders()->addHeaderLine(
            'Content-Disposition',
            'attachment; filename="' . $doa->parseFileName() . '.' . $doa->getContentType()->getExtension() . '"'
        )
            ->addHeaderLine('Pragma: public')->addHeaderLine(
                'Content-Type: ' . $doa->getContentType()->getContentType()
            )->addHeaderLine(
                'Content-Length: '
                . $doa->getSize()
            );

        return $response;
    }
}
