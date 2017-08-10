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

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use Organisation\Entity\AbstractEntity;
use Zend\Form\Form;

/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
class CreateObject extends Form
{
    /**
     * CreateObject constructor.
     *
     * @param EntityManager $entityManager
     * @param AbstractEntity $object
     */
    public function __construct(EntityManager $entityManager, AbstractEntity $object)
    {
        parent::__construct($object->get("underscore_entity_name"));

        /**
         * There is an option to drag the fieldset from the serviceManager,
         * We then need to check if if an factory is present,
         * If not we will use the default ObjectFieldset
         */

        $objectSpecificFieldset = __NAMESPACE__ . '\\' . $object->get('entity_name') . 'Fieldset';

        /**
         * Load a specific fieldSet when present
         */
        if (class_exists($objectSpecificFieldset)) {
            $objectFieldset = new $objectSpecificFieldset($entityManager, $object);
        } else {
            $objectFieldset = new ObjectFieldset($entityManager, $object);
        }
        $objectFieldset->setUseAsBaseFieldset(true);
        $this->add($objectFieldset);


        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'delete',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-delete"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'restore',
                'attributes' => [
                    'class' => "btn btn-info",
                    'value' => _("txt-restore"),
                ],
            ]
        );
    }
}
