<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Admin
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
declare(strict_types=1);

namespace Organisation\Service;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Organisation\Entity\AbstractEntity;
use Organisation\Form\CreateObject;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FormService
 *
 * @package Organisation\Service
 */
class FormService
{
    private ContainerInterface $container;
    private EntityManager $entityManager;

    public function __construct(ContainerInterface $container, EntityManager $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function prepare($classNameOrEntity, array $data = [], array $options = []): Form
    {
        if (!$classNameOrEntity instanceof AbstractEntity) {
            $classNameOrEntity = new $classNameOrEntity();
        }

        $form = $this->getForm($classNameOrEntity, $options);
        $form->setData($data);

        return $form;
    }

    private function getForm(AbstractEntity $entity, array $options = []): Form
    {
        $formName = $entity->get('entity_form_name');
        $filterName = $entity->get('entity_inputfilter_name');

        /**
         * The filter and the form can dynamically be created by pulling the form from the serviceManager
         * if the form or filter is not give in the serviceManager we will create it by default
         */
        if ($this->container->has($formName)) {
            $form = $this->container->build($formName, $options);
        } else {
            $form = new CreateObject($this->entityManager, $entity, $this->container);
        }

        if ($this->container->has($filterName)) {
            /** @var InputFilter $filter */
            $filter = $this->container->get($filterName);
            $form->setInputFilter($filter);
        }

        $form->setAttribute('role', 'form');
        $form->setAttribute('action', '');
        $form->setAttribute('class', 'form-horizontal');

        $form->bind($entity);

        return $form;
    }
}
