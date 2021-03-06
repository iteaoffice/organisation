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

use Laminas\Form\Element\File;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;

/**
 * Class ImportForm
 * @package Organisation\Form\Parent
 */
final class ImportForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'    => File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-file',
                    'help-block' => 'txt-parent-import-file-requirements',
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'upload',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-upload'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'import',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-import'),
                ],
            ]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'file' => [
                'required'   => true,
                'validators' => [
                    new Size(
                        [
                            'min' => '1kB',
                            'max' => '16MB',
                        ]
                    ),
                    new MimeType(
                        [
                            'text/plain',
                        ]
                    ),
                ],
            ],
        ];
    }
}
