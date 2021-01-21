<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Organisation\Entity;
use Program\Entity\Program;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;

/**
 * Class ParentFilter
 *
 * @package Organisation\Form
 */
final class ParentFilterForm extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => Text::class,
                'name'       => 'search',
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-search'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'    => MultiCheckbox::class,
                'name'    => 'memberType',
                'options' => [
                    'inline'        => true,
                    'value_options' => Entity\ParentEntity::getMemberTypeTemplates(),
                    'label'         => _("txt-member-type"),
                ],
            ]
        );
        $filterFieldset->add(
            [
                'type'    => MultiCheckbox::class,
                'name'    => 'artemisiaMemberType',
                'options' => [
                    'inline'        => true,
                    'value_options' => Entity\ParentEntity::getArtemisiaMemberTypeTemplates(),
                    'label'         => _("txt-artemisia-member-type"),
                ],
            ]
        );
        $filterFieldset->add(
            [
                'type'    => MultiCheckbox::class,
                'name'    => 'epossMemberType',
                'options' => [
                    'inline'        => true,
                    'value_options' => Entity\ParentEntity::getEpossMemberTypeTemplates(),
                    'label'         => _("txt-eposs-member-type"),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'    => EntityMultiCheckbox::class,
                'name'    => 'type',
                'options' => [
                    'target_class'   => Entity\Parent\Type::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'type' => 'ASC',
                            ],
                        ],
                    ],
                    'inline'         => true,
                    'object_manager' => $entityManager,
                    'label'          => _("txt-type"),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type'    => EntityMultiCheckbox::class,
                'name'    => 'program',
                'options' => [
                    'target_class'   => Program::class,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],
                            'orderBy'  => [
                                'program' => 'ASC',
                            ],
                        ],
                    ],
                    'inline'         => true,
                    'object_manager' => $entityManager,
                    'label'          => _("txt-has-doa-for"),
                ],
            ]
        );


        $this->add($filterFieldset);

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-filter'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'clear',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
