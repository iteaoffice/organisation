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

use Laminas\Form\Form;
use Laminas\Form\Element\Submit;
use Contact\Form\Element\Contact;
use Organisation\Form\Element\Organisation;

/**
 * Class AddOrganisation
 *
 * @package Organisation\Form
 */
final class AddOrganisation extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->add(
            [
                'type'       => Organisation::class,
                'name'       => 'organisation',
                'options'    => [
                    'help-block' => _('txt-parent-add-organisation-organisation-help-block'),
                ],
                'attributes' => [
                    'label' => _('txt-parent-add-organiation-organisation-label'),
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
