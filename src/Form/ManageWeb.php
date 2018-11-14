<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */
declare(strict_types=1);

namespace Organisation\Form;

use Organisation\Entity\Organisation;
use Organisation\Entity\Web;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Class InvolvedSelect
 *
 * @package Organisation\Form
 */
class ManageWeb extends Form
{
    /**
     * ManageWeb constructor.
     * @param Organisation $organisation
     */
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
                    'type'    => 'Zend\Form\Element\Checkbox',
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
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-save'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submitAndContinue',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-update-and-continue'),
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
    }
}
