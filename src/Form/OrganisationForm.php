<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use Organisation\Entity;
use Zend\Form\Form;
use Zend\Form\Element;

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

        $this->add(
            [
                'type'       => Element\Textarea::class,
                'name'       => 'description',
                'attributes' => [
                    'rows' => 12,
                ],
                'options'    => [
                    "label"      => "txt-description",
                    "help-block" => _("txt-organisation-description-help-block"),
                ],
            ]
        );

        $this->add(
            [
                'type'    => Element\File::class,
                'name'    => 'file',
                'options' => [
                    "label"      => "txt-logo",
                    "help-block" => _("txt-organisation-logo-requirements"),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-delete"),
                ],
            ]
        );
    }
}
