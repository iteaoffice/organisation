<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Organisation\Form;

use General\Entity\VatType;
use General\Service\GeneralService;
use Organisation\Entity;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

/**
 *
 */
class Financial extends Form
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $financial = new Entity\Financial();
        parent::__construct($financial->get('underscore_entity_name'));

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $this->serviceManager = $serviceManager;
        $entityManager
            = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $organisationFieldset = new ObjectFieldset($entityManager, $financial);
        $organisationFieldset->setUseAsBaseFieldset(true);
        $this->add($organisationFieldset);

        //Vat enforcement
        /**
         * @var $generalService GeneralService
         */
        $generalService = $serviceManager->get(GeneralService::class);
        $vatTypes = [0 => '-- No enforcement'];

        /**
         * @var $vatType VatType
         */
        foreach ($generalService->findAll('vatType') as $vatType) {
            $vatTypes[$vatType->getId()] = $vatType->getType();
        }

        $this->add([
            'type'       => 'Zend\Form\Element\Select',
            'name'       => 'vatType',
            'options'    => [
                'value_options' => $vatTypes,
                'help-block'    => _("txt-vat-type-help-block"),
            ],
            'attributes' => [
                'label' => _("txt-vat-type"),
            ],
        ]);

        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => "btn btn-primary",
                'value' => _("txt-submit"),
            ],
        ]);
        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'cancel',
            'attributes' => [
                'class' => "btn btn-warning",
                'value' => _("txt-cancel"),
            ],
        ]);
        $this->add([
            'type'       => 'Zend\Form\Element\Submit',
            'name'       => 'delete',
            'attributes' => [
                'class' => "btn btn-danger",
                'value' => _("txt-delete"),
            ],
        ]);
    }
}
