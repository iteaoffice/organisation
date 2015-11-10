<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Service;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormService implements ServiceLocatorAwareInterface
{
    /**
     * @var Form
     */
    protected $form;
    /**
     * @var \Organisation\Service\OrganisationService
     */
    protected $organisationService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param null $className
     * @param null $entity
     * @param bool $bind
     *
     * @return array|object
     */
    public function getForm($className = null, $entity = null, $bind = true)
    {
        if (!is_null($className) && is_null($entity)) {
            $entity = $this->getOrganisationService()->getEntity($className);
        }

        if (!is_object($entity)) {
            throw new \InvalidArgumentException("No entity created given");
        }
        $formName = 'organisation_' . $entity->get('underscore_entity_name') . '_form';
        $form = $this->getServiceLocator()->get($formName);
        $filterName = 'organisation_' . $entity->get('underscore_entity_name') . '_form_filter';
        $filter = $this->getServiceLocator()->get($filterName);

        $form->setInputFilter($filter);
        if ($bind) {
            $form->bind($entity);
        }

        return $form;
    }

    /**
     * @param      $className
     * @param null $entity
     * @param      $data
     *
     * @return Form
     */
    public function prepare($className, $entity = null, $data = [])
    {
        $form = $this->getForm($className, $entity, true);
        $form->setData($data);

        return $form;
    }

    /**
     * @param OrganisationService $organisationService
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;
    }

    /**
     * Get organisationService.
     *
     * @return OrganisationService.
     */
    public function getOrganisationService()
    {
        if (null === $this->organisationService) {
            $this->organisationService = $this->getServiceLocator()->get(OrganisationService::class);
        }

        return $this->organisationService;
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get the service locator.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
