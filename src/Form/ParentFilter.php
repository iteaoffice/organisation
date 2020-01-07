<?php

/**
*
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
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

/**
 * Class ParentFilter
 *
 * @package Organisation\Form
 */
final class ParentFilter extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => 'Laminas\Form\Element\Text',
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
                    'value_options' => Entity\OParent::getMemberTypeTemplates(),
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
                    'value_options' => Entity\OParent::getArtemisiaMemberTypeTemplates(),
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
                    'value_options' => Entity\OParent::getEpossMemberTypeTemplates(),
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
                'type'       => 'Laminas\Form\Element\Submit',
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
                'type'       => 'Laminas\Form\Element\Submit',
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
