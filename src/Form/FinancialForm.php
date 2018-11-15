<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Doctrine\ORM\EntityManager;
use General\Entity\VatType;
use Organisation\Entity;
use Zend\Form\Form;

/**
 * Class FinancialForm
 *
 * @package Organisation\Form
 */
final class FinancialForm extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        $financial = new Entity\Financial();
        parent::__construct($financial->get('underscore_entity_name'));

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $organisationFieldset = new ObjectFieldset($entityManager, $financial);
        $organisationFieldset->setUseAsBaseFieldset(true);
        $this->add($organisationFieldset);


        $vatTypes = [0 => '-- No enforcement'];

        /** @var VatType $vatType */
        foreach ($entityManager->getRepository(VatType::class)->findAll() as $vatType) {
            $vatTypes[$vatType->getId()] = $vatType->getType();
        }

        $this->add(
            [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'vatType',
                'options'    => [
                    'value_options' => $vatTypes,
                    'help-block'    => _("txt-vat-type-help-block"),
                ],
                'attributes' => [
                    'label' => _("txt-vat-type"),
                ],
            ]
        );

        $this->add(
            [
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf',
            ]
        );
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'submit',
                'attributes' => [
                    'class' => "btn btn-primary",
                    'value' => _("txt-submit"),
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
        $this->add(
            [
                'type'       => 'Zend\Form\Element\Submit',
                'name'       => 'delete',
                'attributes' => [
                    'class' => "btn btn-danger",
                    'value' => _("txt-delete"),
                ],
            ]
        );
    }
}
