<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     Controller
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Organisation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Organisation\Service\OrganisationService;
use Organisation\Service\FormServiceAwareInterface;
use Organisation\Service\FormService;
use Organisation\Entity;

/**
 * @category    Organisation
 * @package     Controller
 */
class OrganisationController extends AbstractActionController implements
    FormServiceAwareInterface, ServiceLocatorAwareInterface
{
    /**
     * @var OrganisationService
     */
    protected $organisationService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Message container
     * @return array|void
     */
    public function indexAction()
    {
    }

    /**
     * Give a list of organisations
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationsAction()
    {
        $organisations = $this->getOrganisationService()->findAll('organisation');

        return new ViewModel(array('organisations' => $organisations));
    }

    /**
     * Show the details of 1 organisation
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationAction()
    {
        $organisation = $this->getOrganisationService()->findEntityById(
            'organisation',
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        return new ViewModel(array('organisation' => $organisation));
    }


    /**
     * Edit an entity
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $this->layout(false);
        $entity = $this->getOrganisationService()->findEntityById(
            $this->getEvent()->getRouteMatch()->getParam('entity'),
            $this->getEvent()->getRouteMatch()->getParam('id')
        );

        $form = $this->getFormService()->prepare($entity->get('entity_name'), $entity, $_POST);
        $form->setAttribute('class', 'form-vertical live-form-edit');
        $form->setAttribute('id', 'organisation-' . strtolower($entity->get('entity_name')) . '-' . $entity->getId());

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->getOrganisationService()->updateEntity($form->getData());

            $view = new ViewModel(array($this->getEvent()->getRouteMatch()->getParam('entity') => $form->getData()));
            $view->setTemplate(
                "organisation/partial/" . $this->getEvent()->getRouteMatch()->getParam('entity') . '.twig'
            );

            return $view;
        }

        return new ViewModel(array('form' => $form, 'entity' => $entity));
    }

    /**
     * Trigger to switch layout
     *
     * @param $layout
     */
    public function layout($layout)
    {
        if (false === $layout) {
            $this->getEvent()->getViewModel()->setTemplate('layout/nolayout');
        } else {
            $this->getEvent()->getViewModel()->setTemplate('layout/' . $layout);
        }
    }

    /**
     * @return FormService
     */
    public function getFormService()
    {
        return $this->formService;
    }

    /**
     * @param $formService
     *
     * @return OrganisationController
     */
    public function setFormService($formService)
    {
        $this->formService = $formService;

        return $this;
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
     * @return OrganisationController
     */
    public function setOrganisationService($organisationService)
    {
        $this->organisationService = $organisationService;

        return $this;
    }
}
