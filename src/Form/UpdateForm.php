<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use Organisation\Entity;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;

/**
 * Class UpdateForm
 *
 * @package Organisation\Form
 */
final class UpdateForm extends Form implements InputFilterProviderInterface
{
    public function __construct(EntityManager $entityManager)
    {
        $update = new Entity\Update();
        parent::__construct($update->get('underscore_entity_name'));

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $updateFieldset = new ObjectFieldset($entityManager, $update);
        $updateFieldset->setUseAsBaseFieldset(true);
        $this->add($updateFieldset);

        $this->add(
            [
                'type' => Element\Csrf::class,
                'name' => 'csrf',
            ]
        );

        $this->add(
            [
                'type'    => Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-logo',
                    'help-block' => _('txt-organisation-update-logo-requirements'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Element\Submit::class,
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
            'file' => [
                'required'   => false,
                'validators' => [
                    new Size(
                        [
                            'min' => '10kb',
                            'max' => '8MB',
                        ]
                    ),
                    new ImageSize(
                        [
                            'minWidth' => '400',
                        ]
                    ),
                    new MimeType(
                        [
                            'image/png',
                            'image/jpg',
                        ]
                    ),
                ],
            ],

        ];
    }
}
