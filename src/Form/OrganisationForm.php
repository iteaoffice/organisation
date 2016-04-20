<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use Organisation\Entity;
use Zend\Form\Form;

/**
 * Class OrganisationForm
 *
 * @package Organisation\Form
 */
class OrganisationForm extends Form
{
    /**
     * OrganisationForm constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $organisation = new Entity\Organisation();
        parent::__construct($organisation->get('underscore_entity_name'));

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $organisationFieldset = new ObjectFieldset($entityManager, $organisation);
        $organisationFieldset->setUseAsBaseFieldset(true);
        $this->add($organisationFieldset);

        $this->add([
            'type'       => '\Zend\Form\Element\Textarea',
            'name'       => 'description',
            'attributes' => [
                'rows' => 12
            ],
            'options'    => [
                "label"      => "txt-description",
                "help-block" => _("txt-organisation-description-help-block"),
            ],
        ]);

        $this->add([
            'type'    => '\Zend\Form\Element\File',
            'name'    => 'file',
            'options' => [
                "label"      => "txt-logo",
                "help-block" => _("txt-organisation-logo-requirements"),
            ],
        ]);

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
            'name'       => 'cancel',
            'attributes' => [
                'class' => "btn btn-warning",
                'value' => _("txt-cancel"),
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
    }
}
