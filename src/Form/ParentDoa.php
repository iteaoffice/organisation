<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;

/**
 *
 */
class ParentDoa extends Form implements InputFilterProviderInterface
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            [
                'type'    => \Zend\Form\Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-file',
                    'help-block' => _('txt-a-signed-doa-is-required'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => \Zend\Form\Element\Date::class,
                'name'    => 'dateSigned',
                'options' => [
                    'label'      => 'txt-date-signed',
                    'help-block' => _('txt-partner-doa-date-signed-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => \Zend\Form\Element\Date::class,
                'name'    => 'dateApproved',
                'options' => [
                    'label'      => 'txt-date-approved',
                    'help-block' => _('txt-partner-doa-date-approved-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'    => 'Contact\Form\Element\Contact',
                'name'    => 'contact',
                'options' => [
                    'label'      => 'txt-contact',
                    'help-block' => _('txt-partner-doa-contact-help-block'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-upload-parent-doa'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'delete',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-delete'),
                ],
            ]
        );
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'dateSigned'   => [
                'required' => true,
            ],
            'dateApproved' => [
                'required' => false,
            ],
            'file'         => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '5kB',
                            'max' => '8MB',
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
