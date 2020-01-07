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

use Contact\Entity\Contact;
use General\Entity\Country;
use General\Service\CountryService;
use Organisation\Entity;
use Organisation\Entity\OParent;
use Organisation\Service\OrganisationService;
use Laminas\Form\Form;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;

/**
 * Class Financial
 * @package Organisation\Form
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

            if (! array_key_exists($country['id'], $financialOrganisationValueOptions)) {
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
                'type'       => Select::class,
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
                'type'       => Text::class,
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

        foreach ($parent->getFinancial() as $financialOrganisation) {
            foreach ($financialOrganisation->getOrganisation()->getContactOrganisation() as $contactOrganisation) {
                /** @var Contact $contact */
                $contact = $contactOrganisation->getContact();

                $financialContactValueOptions[$contact->getId()] = $contact->getFormName();
            }
        }

        asort($financialContactValueOptions);

        $this->add(
            [
                'type'       => Select::class,
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
                'type'       => Radio::class,
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
                'type'       => Textarea::class,
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
                'type'       => Text::class,
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
                'type'       => Text::class,
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
                'type'       => Select::class,
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
                'type'       => Radio::class,
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
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-update'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-delete'),
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
