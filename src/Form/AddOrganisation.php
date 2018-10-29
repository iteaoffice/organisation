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

use Zend\Form\Form;

/**
 * Class AddOrganisation
 *
 * @package Organisation\Form
 */
final class AddOrganisation extends Form
{
    /**
     * AddOrganisation constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->add(
            [
                'type'       => 'Organisation\Form\Element\Organisation',
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
                'type'       => 'Contact\Form\Element\Contact',
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
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
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
