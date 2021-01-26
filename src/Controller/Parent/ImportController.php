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
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use Organisation\Controller\AbstractController;
use Organisation\Entity;
use Organisation\Form;
use Organisation\Service\FormService;
use Organisation\Service\OrganisationService;
use Organisation\Service\ParentService;

use function array_merge_recursive;
use function set_time_limit;

/**
 * Class ParentController
 * @package Organisation\Controller
 */
final class ImportController extends AbstractController
{
    public function projectAction(): ViewModel
    {
        set_time_limit(0);

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new Form\Parent\ImportForm();
        $form->setData($data);

        /** store the data in the session, so we can use it when we really handle the import */
        $importSession = new Container('import');

        $handleImport = null;
        if ($this->getRequest()->isPost()) {
            if (isset($data['upload']) && $form->isValid()) {
                $fileData = file_get_contents($data['file']['tmp_name']);

                $importSession->active   = true;
                $importSession->fileData = $fileData;

                $handleImport = $this->handleParentAndProjectImport(
                    $fileData,
                    [],
                    false
                );
            }

            if (isset($data['import'], $data['key']) && $importSession->active) {
                $handleImport = $this->handleParentAndProjectImport(
                    $importSession->fileData,
                    $data['key'],
                    true
                );
            }
        }

        return new ViewModel(['form' => $form, 'handleImport' => $handleImport]);
    }
}
