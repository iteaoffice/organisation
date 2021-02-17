<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Form\Parent;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Laminas\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Program\Entity\Program;

/**
 * Class CreateParentDoaForm
 * @package Organisation\Form\Parent
 */
final class CreateParentDoaForm extends Form\Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
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
                                'program' => Criteria::ASC,
                            ],
                        ],
                    ],
                    'object_manager' => $entityManager,
                    'help-block'     => _('txt-add-doa-for-program-help-block'),
                    'label'          => _('txt-add-doa-for-program-label'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-add-parent-doa'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'program' => [
                'required' => true,
            ],
        ];
    }
}
