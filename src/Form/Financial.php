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

use Contact\Entity\Contact;
use General\Entity\Country;
use General\Service\CountryService;
use Organisation\Entity;
use Organisation\Entity\OParent;
use Organisation\Service\OrganisationService;
use Zend\Form\Form;

/**
 * Class Financial
 *
 * @package Organisation\Form
 * @deprecated
 */
final class Financial extends Form
{
    public function __construct(
        OParent $parent,
        CountryService $countryService,
        OrganisationService $organisationService
    ) {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $countries = [];
        /** @var Country $country */
        foreach ($countryService->findAll(Country::class) as $country) {
            $countries[$country->getId()] = $country->getCountry();
        }
        asort($countries);

        $financialOrganisationValueOptions = [];

        /** @var Financial $financial */
        foreach ($organisationService->findOrganisationFinancialList(['order' => 'organisation', 'direction' => 'asc'])
                ->getArrayResult() as $financialOrganisation) {
            $country = $financialOrganisation['organisation']['country'];

            if (!array_key_exists($country['id'], $financialOrganisationValueOptions)) {
                $financialOrganisationValueOptions[$country['id']] = [
                    'label'   => $country['country'],
                    'options' => []
                ];
            }

            $financialOrganisationValueOptions[$country['id']]['options'][$financialOrganisation['id']] = sprintf(
                '%s (VAT: %s)',
                $financialOrganisation['organisation']['organisation'],
                $financialOrganisation['vat']
            );
        }

        asort($financialOrganisationValueOptions);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'organisationFinancial',
                'options'    => [
                    'value_options' => $financialOrganisationValueOptions,
                    'label'         => _('txt-financial-organisation-name'),
                    'help-block'    => _('txt-parent-financial-financial-organisation-name-help-block'),
                ],
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'branch',
                'options'    => [
                    'label'      => _('txt-branch'),
                    'help-block' => _('txt-financial-organisation-branch-explanation'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-financial-organisation-branch-placeholder'),
                ],
            ]
        );

        $financialContactValueOptions[$parent->getContact()->getId()] = $parent->getContact()->getFormName();
        /**
         * Add the contacts from the organisations
         */
        foreach ($parent->getParentOrganisation() as $parentOrganisation) {
            foreach ($parentOrganisation->getOrganisation()->getContactOrganisation() as $contactOrganisation) {
                /** @var Contact $contact */
                $contact = $contactOrganisation->getContact();

                $financialContactValueOptions[$contact->getId()] = $contact->getFormName();
            }
        }

        asort($financialContactValueOptions);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'contact',
                'options'    => [
                    'value_options' => $financialContactValueOptions,
                    'label'         => _('txt-financial-contact'),
                ],
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Radio',
                'name'       => 'omitContact',
                'options'    => [
                    'value_options' => Entity\Financial::getOmitContactTemplates(),
                    'label'         => _('txt-omit-contact'),
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Textarea',
                'name'       => 'address',
                'options'    => [
                    'label' => _('txt-address'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-address-placeholder'),
                    'required'    => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'zipCode',
                'options'    => [
                    'label' => _('txt-zip-code'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-zip-code-placeholder'),
                    'required'    => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'city',
                'options'    => [
                    'label' => _('txt-city'),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _('txt-city-placeholder'),
                    'required'    => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'country',
                'options'    => [
                    'value_options' => $countries,
                    'label'         => _('txt-country'),
                ],
                'attributes' => [
                    'class'    => 'form-control',
                    'required' => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Radio',
                'name'       => 'preferredDelivery',
                'options'    => [
                    'value_options' => Entity\Financial::getEmailTemplates(),
                    'label'         => _('txt-preferred-delivery'),
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-update'),
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
