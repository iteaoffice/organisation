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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Form;

use General\Entity\Country;
use General\Service\GeneralService;
use Organisation\Entity\Financial as FinancialOrganisation;
use Organisation\Entity\OParent;
use Organisation\Service\OrganisationService;
use Zend\Form\Form;

/**
 * Class Financial
 *
 * @package Parent\Form
 */
class Financial extends Form
{
    /**
     * Financial constructor.
     *
     * @param OParent             $parent
     * @param GeneralService      $generalService
     * @param OrganisationService $organisationService
     */
    public function __construct(
        OParent $parent,
        GeneralService $generalService,
        OrganisationService $organisationService
    ) {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');
        $countries = [];
        foreach ($generalService->findAll(Country::class) as $country) {
            $countries[$country->getId()] = $country->getCountry();
        }
        asort($countries);

        $financialOrganisationValueOptions = [];

        /** @var \Organisation\Entity\Financial $financial */
        foreach ($organisationService->findOrganisationFinancialList(['order' => 'organisation', 'direction' => 'asc'])
                                ->getArrayResult() as $financial) {
            $financialOrganisationValueOptions[$financial['id']] = sprintf(
                "%s (%s)",
                $financial['organisation']['organisation'],
                $financial['vat']
            );
        }

        asort($financialOrganisationValueOptions);

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'organisationFinancial',
                'options'    => [
                    'value_options' => $financialOrganisationValueOptions,
                    'label'         => _("txt-financial-organisation-name"),
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
                    'label'      => _("txt-branch"),
                    'help-block' => _("txt-financial-organisation-branch-explanation"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-financial-organisation-branch-placeholder"),
                ],
            ]
        );

        $financialContactValueOptions[$parent->getContact()->getId()]
            = $parent->getContact()->getFormName();
        /**
         * Add the contacts from the organisations
         */
        foreach ($parent->getParentOrganisation() as $parentOrganisation) {
            foreach ($parentOrganisation->getOrganisation()->getContactOrganisation() as $contactOrganisation) {
                $financialContactValueOptions[$contactOrganisation->getContact()->getId()]
                    = $contactOrganisation->getContact()->getFormName();
            }
        }

        asort($financialContactValueOptions);

        $this->add(
            [
                'type'       => 'Contact\Form\Element\Contact',
                'name'       => 'contact',
                'options'    => [
                    'value_options' => $financialContactValueOptions,
                    'label'         => _("txt-financial-contact"),
                ],
                'attributes' => [
                    'class'    => 'form-control',
                    'required' => true,
                ],
            ]
        );
        $organisationFinancial = new FinancialOrganisation();
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Radio',
                'name'       => 'omitContact',
                'options'    => [
                    'value_options' => \Organisation\Entity\Financial::getOmitContactTemplates(),
                    'label'         => _("txt-omit-contact"),
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
                    'label' => _("txt-address"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-address-placeholder"),
                    'required'    => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'zipCode',
                'options'    => [
                    'label' => _("txt-zip-code"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-zip-code-placeholder"),
                    'required'    => true,
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Text',
                'name'       => 'city',
                'options'    => [
                    'label' => _("txt-city"),
                ],
                'attributes' => [
                    'class'       => 'form-control',
                    'placeholder' => _("txt-city-placeholder"),
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
                    'label'         => _("txt-country"),
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
                    'value_options' => \Organisation\Entity\Financial::getEmailTemplates(),
                    'label'         => _("txt-preferred-delivery"),
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
                    'class' => "btn btn-primary",
                    'value' => _("txt-update"),
                ],
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'cancel',
                'attributes' => [
                    'class' => "btn btn-warning",
                    'value' => _("txt-cancel"),
                ],
            ]
        );
    }
}
