<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
 */

namespace Organisation\Form;

use Organisation\Service\OrganisationService;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Jield copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
 */
class OrganisationFilter extends Form
{
    /**
     * @param OrganisationService $organisationService
     */
    public function __construct(OrganisationService $organisationService)
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'search',
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-search'),
                ],
            ]
        );

        $types = [];
        foreach ($organisationService->findAll('type') as $type) {
            $types[$type->getId()] = $type->getType();
        }

        $filterFieldset->add(
            [
                'type'       => 'Zend\Form\Element\MultiCheckbox',
                'name'       => 'type',
                'options'    => [
                    'inline'        => true,
                    'value_options' => $types
                ],
                'attributes' => [
                    'label' => _("txt-organisation-type"),
                ],
            ]
        );

        $this->add($filterFieldset);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'id'    => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-filter'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'clear',
                'attributes' => [
                    'id'    => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
