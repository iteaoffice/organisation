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

use Contact\Form\Element\Contact;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntitySelect;
use Program\Entity\Program;
use Laminas\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\Size;

/**
 * Class ParentDoa
 *
 * @package Organisation\Form
 */
final class ParentDoa extends Form\Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            [
                'type'    => Form\Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-file',
                    'help-block' => _('txt-a-signed-doa-is-required'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => Form\Element\Date::class,
                'name'    => 'dateSigned',
                'options' => [
                    'label'      => 'txt-date-signed',
                    'help-block' => _('txt-partner-doa-date-signed-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => Form\Element\Date::class,
                'name'    => 'dateApproved',
                'options' => [
                    'label'      => 'txt-date-approved',
                    'help-block' => _('txt-partner-doa-date-approved-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => EntitySelect::class,
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
                    'help-block'     => _('txt-parent-doa-valid-for-program-help-block'),
                    'label'          => _('txt-parent-doa-valid-for-program-label'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => Contact::class,
                'name'    => 'contact',
                'options' => [
                    'label'      => 'txt-contact',
                    'help-block' => _('txt-partner-doa-contact-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-upload-parent-doa'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Form\Element\Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-delete'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'dateSigned'   => [
                'required' => true,
            ],
            'dateApproved' => [
                'required' => true,
            ],
            'file'         => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '1kB',
                            'max' => '16MB',
                        ]
                    ),
                    new Extension(
                        [
                            'extension' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
                        ]
                    ),
                ],
            ],
        ];
    }
}
