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

use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Organisation\Entity;
use Organisation\Service\ParentService;
use Program\Entity\Program;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class ParentFilter
 *
 * @package Organisation\Form
 */
class ParentFilter extends Form
{
    /**
     * @param ParentService $parentService
     */
    public function __construct(ParentService $parentService)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\Text',
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
                    'object_manager' => $parentService->getEntityManager(),
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
                    'object_manager' => $parentService->getEntityManager(),
                    'label'          => _("txt-has-doa-for"),
                ],
            ]
        );


        $this->add($filterFieldset);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
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
                'type'       => 'Zend\Form\Element\Submit',
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
