<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Form;

use Organisation\Entity;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Zend\Form\Form;

/**
 *
 */
class AddAffiliation extends Form
{
    /**
     * AddAffiliation constructor.
     *
     * @param ProjectService      $projectService
     * @param Entity\Organisation $organisation
     */
    public function __construct(
        ProjectService $projectService,
        Entity\Organisation $organisation
    ) {
        parent::__construct();

        $currentProjects = [];
        /**
         * @var $projectService ProjectService
         */
        foreach ($projectService->findProjectByOrganisation($organisation, ProjectService::WHICH_ALL) as $project) {
            $currentProjects[] = $project->getId();
        }

        $projects = [];
        /**
         * @var $newProject Project
         */
        foreach ($projectService->findAllProjects(ProjectService::WHICH_ALL)->getResult() as $newProject) {
            if (!in_array($newProject->getId(), $currentProjects)) {
                $projects[$newProject->getId()] = sprintf("%s", $newProject);
            }
        }

        arsort($projects);

        $this->add([
            'type'       => 'Zend\Form\Element\Select',
            'name'       => 'project',
            'options'    => [
                'value_options' => $projects,
                'help-block'    => _("txt-project-help-block")
            ],
            'attributes' => [
                'label' => _("txt-project")
            ],
        ]);


        $this->add([
            'type'       => 'Zend\Form\Element\Text',
            'name'       => 'branch',
            'options'    => [
                'help-block' => _("txt-branch-help-block")
            ],
            'attributes' => [
                'label' => _("txt-branch"),
            ],
        ]);

        $contacts = [];
        foreach ($organisation->getContactOrganisation() as $contactOrganisation) {
            $contacts[$contactOrganisation->getContact()->getId()] = $contactOrganisation->getContact()->getFormName();
        }

        asort($contacts);

        $this->add([
            'type'       => 'Contact\Form\Element\Contact',
            'name'       => 'contact',
            'options'    => [
                'value_options' => $contacts,
                'help-block'    => _("txt-technical-contact-help-block")
            ],
            'attributes' => [
                'label' => _("txt-technical-contact")
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
    }
}
