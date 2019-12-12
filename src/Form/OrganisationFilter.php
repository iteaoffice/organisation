<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Organisation\Entity\Financial;
use Organisation\Entity\Type;
use Organisation\Service\OrganisationService;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Text;

/**
 * Class OrganisationFilter
 * @package Organisation\Form
 */
final class OrganisationFilter extends Form
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
                'type' => Text::class,
                'name' => 'search',
                'attributes' => [
                    'class' => 'form-control',
                    'placeholder' => _('txt-search'),
                ],
            ]
        );

        $types = [];
        /** @var Type $type */
        foreach ($organisationService->findAll(Type::class) as $type) {
            $types[$type->getId()] = $type->getType();
        }

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'type',
                'options' => [
                    'inline' => true,
                    'value_options' => $types,
                ],
                'attributes' => [
                    'label' => _('txt-organisation-type'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'options',
                'options' => [
                    'inline' => true,
                    'value_options' => [
                        1 => _('txt-active-in-project'),
                        2 => _('txt-is-parent'),
                    ],
                ],
                'attributes' => [
                    'label' => _('txt-options-type'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'vatStatus',
                'options' => [
                    'inline' => true,
                    'value_options' => Financial::getVatStatusTemplates(),
                ],
                'attributes' => [
                    'label' => _('txt-vat-status'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'omitContact',
                'options' => [
                    'inline' => true,
                    'value_options' => Financial::getOmitContactTemplates(),
                ],
                'attributes' => [
                    'label' => _('txt-omit-contact'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'requiredPurchaseOrder',
                'options' => [
                    'inline' => true,
                    'value_options' => Financial::getRequiredPurchaseOrderTemplates(),
                ],
                'attributes' => [
                    'label' => _('txt-required-purchase-order'),
                ],
            ]
        );

        $filterFieldset->add(
            [
                'type' => MultiCheckbox::class,
                'name' => 'email',
                'options' => [
                    'inline' => true,
                    'value_options' => Financial::getEmailTemplates(),
                ],
                'attributes' => [
                    'label' => _('txt-email'),
                ],
            ]
        );

        $this->add($filterFieldset);

        $this->add(
            [
                'type' => Submit::class,
                'name' => 'submit',
                'attributes' => [
                    'id' => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-filter'),
                ],
            ]
        );

        $this->add(
            [
                'type' => Submit::class,
                'name' => 'clear',
                'attributes' => [
                    'id' => 'cancel',
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
