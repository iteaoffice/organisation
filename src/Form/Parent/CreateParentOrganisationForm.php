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

use Contact\Form\Element\Contact;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Organisation\Form\Element\OrganisationElement;

/**
 * Class AddOrganisationForm
 * @package Organisation\Form\Parent
 */
final class CreateParentOrganisationForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->add(
            [
                'type'       => OrganisationElement::class,
                'name'       => 'organisation',
                'options'    => [
                    'help-block' => _('txt-parent-add-organisation-organisation-help-block'),
                ],
                'attributes' => [
                    'label' => _('txt-parent-add-organisation-organisation-label'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Contact::class,
                'name'       => 'contact',
                'options'    => [
                    'help-block' => _('txt-parent-add-organisation-contact-help-block'),
                ],
                'attributes' => [
                    'label' => _('txt-parent-add-organisation-contact-label'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
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
