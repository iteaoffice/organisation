<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Organisation\Service;

use Doctrine\ORM\EntityManager;
use Organisation\Form\CreateObject;
use Organisation\InputFilter\Object;
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
            $entity = new $className;
        }

        if (!is_object($entity)) {
            throw new \InvalidArgumentException("No entity created given");
        }

        $formName = 'Organisation\\Form\\' . $entity->get('entity_name');
        $filterName = 'Organisation\\InputFilter\\' . $entity->get('entity_name') . 'Filter';

        /*
         * The filter and the form can dynamically be created by pulling the form from the serviceManager
         * if the form or filter is not give in the serviceManager we will create it by default
         */
        if (!$this->getServiceLocator()->has($formName)) {
            /** @var EntityManager $entityManager */
            $entityManager = $this->getServiceLocator()->get(EntityManager::class);
            $form = new CreateObject($entityManager, new $entity());
        } else {
            $form = $this->getServiceLocator()->get($formName);
        }

        if (!$this->getServiceLocator()->has($filterName)) {
            $filter = new Object();
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
