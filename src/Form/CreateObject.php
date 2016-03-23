<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use Organisation\Entity\EntityAbstract;
use Zend\Form\Form;

/**
 * Class CreateObject
 *
 * @package Invoice\Form
 */
class CreateObject extends Form
{
    /**
     * CreateObject constructor.
     *
     * @param EntityManager  $entityManager
     * @param EntityAbstract $object
     */
    public function __construct(EntityManager $entityManager, EntityAbstract $object)
    {
        parent::__construct($object->get('full_entity_name'));

        $objectSpecificFieldset = '\Organisation\Form\\' . ucfirst($object->get('entity_name')) . 'Fieldset';
        /*
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
        $this->setAttribute('action', '');

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => "btn btn-primary",
                'value' => _("txt-submit"),
            ],
        ]);
        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'delete',
            'attributes' => [
                'class' => "btn btn-danger",
                'value' => _("txt-delete"),
            ],
        ]);
        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'cancel',
            'attributes' => [
                'class' => "btn btn-warning",
                'value' => _("txt-cancel"),
            ],
        ]);
    }
}
