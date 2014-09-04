<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Organisation
 * @package     Controller
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Organisation\Controller;

use Organisation\Service\FormService;
use Organisation\Service\FormServiceAwareInterface;
use Organisation\Service\OrganisationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 *
 */
class OrganisationManagerController extends AbstractActionController implements
    FormServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var OrganisationService;
     */
    protected $organisationService;
    /**
     * @var FormService
     */
    protected $formService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Give a list of messages
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function messagesAction()
    {
        $messages = $this->getOrganisationService()->findAll('message');

        return new ViewModel(['messages' => $messages]);
    }

    /**
     * Gateway to the Organisation Service
     *
     * @return OrganisationService
     */
    public function getOrganisationService()
    {
        return $this->getServiceLocator()->get('organisation_generic_service');
    }

    /**
     * @param $organisationService
     *
     * @return OrganisationManagerController
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OrganisationManagerController|void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Show the details of 1 message
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function messageAction()
    {
        $message = $this->getOrganisationService()->findEntityById(
            'message',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(['message' => $message]);
    }

    /**
     * Create a new entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction()
    {
        $entity = $this->getEvent()->getRouteMatch()->getParam('entity');
        $form = $this->getFormService()->prepare($this->params('entity'), null, $_POST);
        $form->setAttribute('class', 'form-horizontal');
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getOrganisationService()->newEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/organisation-manager/' . strtolower($this->params('entity')),
                ['id' => $result->getId()]
            );
        }

        return new ViewModel(['form' => $form, 'entity' => $entity, 'fullVersion' => true]);
    }

    /**
     * @return \Organisation\Service\FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return OrganisationManagerController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
    }

    /**
     * Edit an entity by finding it and call the corresponding form
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $entity = $this->getOrganisationService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-horizontal live-form');
        $form->setAttribute('id', 'organisation-organisation-' . $entity->getId());
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $result = $this->getOrganisationService()->updateEntity($form->getData());

            return $this->redirect()->toRoute(
                'zfcadmin/organisation/' . strtolower($entity->get('dashed_entity_name')),
                ['id' => $result->getId()]
            );
        }

        return new ViewModel(['form' => $form, 'entity' => $entity, 'fullVersion' => true]);
    }

    /**
     * (soft-delete) an entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function deleteAction()
    {
        $entity = $this->getOrganisationService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );
        $this->getOrganisationService()->removeEntity($entity);

        return $this->redirect()->toRoute(
            'zfcadmin/organisation-manager/' . $entity->get('dashed_entity_name') . 's'
        );
    }
}
