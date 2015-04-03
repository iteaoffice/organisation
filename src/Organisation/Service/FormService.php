<?php
/**
 * Jield copyright message placeholder
 *
 * @category    Organisation
 * @package     Service
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2015 Jield (http://jield.nl)
 */
namespace Organisation\Service;

use Organisation\Form\CreateObject;
use Organisation\Form\FilterCreateObject;
use Zend\Form\Form;

class FormService extends ServiceAbstract
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @param null $className
     * @param null $entity
     * @param bool $bind
     *
     * @return Form
     */
    public function getForm($className = null, $entity = null, $bind = true)
    {
        if (!is_null($className) && is_null($entity)) {
            $entity = $this->getEntity($className);
        }

        if (!is_object($entity)) {
            throw new \InvalidArgumentException("No entity created given");
        }

        $formName = 'Organisation\\' . $entity->get('entity_name') . '\\Form';
        $filterName = 'Organisation\\InputFilter\\' . $entity->get('entity_name');

        /**
         * The filter and the form can dynamically be created by pulling the form from the serviceManager
         * if the form or filter is not give in the serviceManager we will create it by default
         */
        if (!$this->getServiceLocator()->has($formName)) {
            $form = new CreateObject($this->getServiceLocator(), new $entity());
        } else {
            $form = $this->getServiceLocator()->get($formName);
        }

        if (!$this->getServiceLocator()->has($filterName)) {
            $filter = new FilterCreateObject();
        } else {
            $filter = $this->getServiceLocator()->get($filterName);
        }

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
}