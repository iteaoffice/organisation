<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Form\Organisation;

use Laminas\Form\Element;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Submit;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Organisation\Entity\Organisation;
use Organisation\Entity\Web;

final class ManageWebForm extends Form
{
    public function __construct(Organisation $organisation)
    {
        parent::__construct();

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $mainWebFieldset = new Fieldset('webFieldset');

        foreach ($organisation->getWeb() as $web) {
            $webFieldset = new Fieldset($web->getId());

            $webFieldset->add(
                [
                    'type'       => Element\Url::class,
                    'name'       => 'web',
                    'attributes' => [
                        'class' => 'form-control',
                    ],
                ]
            );


            $webFieldset->add(
                [
                    'type'       => Element\Select::class,
                    'name'       => 'main',
                    'options'    => [
                        'value_options' => Web::getMainTemplates()
                    ],
                    'attributes' => [
                        'class' => 'form-control',
                    ],
                ]
            );

            $webFieldset->add(
                [
                    'type'    => Checkbox::class,
                    'name'    => 'delete',
                    'options' => [
                        'label'              => _("txt-delete"),
                        'use_hidden_element' => true,
                        'checked_value'      => 1,
                        'unchecked_value'    => 0,
                    ],
                ]
            );

            $mainWebFieldset->add($webFieldset);
        }

        $this->add($mainWebFieldset);

        $this->add(
            [
                'type'       => Element\Url::class,
                'name'       => 'web',
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Select::class,
                'name'       => 'main',
                'options'    => [
                    'value_options' => Web::getMainTemplates()
                ],
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-save'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submitAndContinue',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-update-and-continue'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
