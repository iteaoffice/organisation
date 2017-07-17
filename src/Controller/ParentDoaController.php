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

use Organisation\Entity;
use Organisation\Form\ParentDoa;
use Organisation\Form\UploadParentDoa;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;
use Zend\View\Model\ViewModel;

/**
 * Class ParentDoaController
 *
 * @package Organisation\Controller
 */
class ParentDoaController extends OrganisationAbstractController
{
    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function uploadAction()
    {
        $parent = $this->getParentService()->findParentById($this->params('parentId'));

        if (is_null($parent)) {
            return $this->notFoundAction();
        }

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new ParentDoa();
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $parent->getId()],
                    []
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
                $doa->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type));

                $doa->setContact($this->getContactService()->findContactById((int)$data['contact']));
                if ($dateSigned = \DateTime::createFromFormat('Y-m-d', $data['dateSigned'])) {
                    $doa->setDateSigned($dateSigned);
                }
                if ($dateApproved = \DateTime::createFromFormat('Y-m-d', $data['dateApproved'])) {
                    $doa->setDateApproved($dateApproved);
                }

                $doa->setParent($parent);
                $doaObject->setDoa($doa);
                $this->getParentService()->newEntity($doaObject);
                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-doa-for-parent-%s-has-been-uploaded"),
                            $parent->getOrganisation()
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $parent->getId()],
                    []
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

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        /** @var Entity\Parent\Doa $doa */
        $doa = $this->getParentService()->findEntityById(Entity\Parent\Doa::class, $this->params('id'));

        if (is_null($doa)) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'dateSigned'   => is_null($doa->getDateSigned()) ? null : $doa->getDateSigned()->format('Y-m-d'),
                'dateApproved' => is_null($doa->getDateApproved()) ? null : $doa->getDateApproved()->format('Y-m-d'),
            ],
            $this->getRequest()->getFiles()->toArray(),
            $this->getRequest()->getPost()->toArray()
        );
        $form = new ParentDoa();
        $form->setData($data);

        $form->get('contact')->injectContact($doa->getContact());
        $form->getInputFilter()->get('file')->setRequired(false);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $doa->getParent()->getId()],
                    []
                );
            }

            if (isset($data['delete'])) {
                $this->getParentService()->removeEntity($doa);

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $doa->getParent()->getId()],
                    []
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
                        $this->getParentService()->newEntity($doaObject);
                    }

                    //Create a article object element
                    $fileSizeValidator = new FilesSize(PHP_INT_MAX);
                    $fileSizeValidator->isValid($fileData['file']);
                    $doa->setSize($fileSizeValidator->size);

                    $fileTypeValidator = new MimeType();
                    $fileTypeValidator->isValid($fileData['file']);
                    $doa->setContentType($this->getGeneralService()->findContentTypeByContentTypeName($fileTypeValidator->type));
                }


                $doa->setContact($this->getContactService()->findContactById((int)$data['contact']));
                if ($dateSigned = \DateTime::createFromFormat('Y-m-d', $data['dateSigned'])) {
                    $doa->setDateSigned($dateSigned);
                }
                if ($dateApproved = \DateTime::createFromFormat('Y-m-d', $data['dateApproved'])) {
                    $doa->setDateApproved($dateApproved);
                }

                $this->getParentService()->updateEntity($doa);

                $this->flashMessenger()->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            $this->translate("txt-doa-for-parent-%s-has-been-uploaded"),
                            $doa->getParent()->getOrganisation()
                        )
                    );

                return $this->redirect()->toRoute(
                    'zfcadmin/parent/view',
                    ['id' => $doa->getParent()->getId()],
                    []
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


    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function downloadAction()
    {
        /**
         * @var Entity\Parent\Doa $doa
         */
        $doa = $this->getParentService()->findEntityById(Entity\Parent\Doa::class, $this->params('id'));
        if (is_null($doa) || count($doa->getObject()) === 0) {
            return $this->notFoundAction();
        }
        /*
         * Due to the BLOB issue, we treat this as an array and we need to capture the first element
         */
        $object = $doa->getObject()->first()->getObject();
        $response = $this->getResponse();
        $response->setContent(stream_get_contents($object));
        $response->getHeaders()->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")->addHeaderLine(
                'Content-Disposition',
                'attachment; filename="' . $doa->parseFileName() . '.' . $doa->getContentType()->getExtension() . '"'
            )
            ->addHeaderLine("Pragma: public")->addHeaderLine(
                'Content-Type: ' . $doa->getContentType()->getContentType()
            )->addHeaderLine('Content-Length: '
                . $doa->getSize());

        return $this->response;
    }
}
