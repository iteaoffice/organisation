<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form;

use Organisation\Entity;
use Project\Entity\Project;
use Project\Service\ProjectService;
use Laminas\Form\Form;
use Laminas\Form\Element\Submit;
use Contact\Form\Element\Contact;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Select;
use function in_array;

/**
 * Class AddAffiliation
 *
 * @package Organisation\Form
 */
final class AddAffiliation extends Form
{
    public function __construct(
        ProjectService $projectService,
        Entity\Organisation $organisation
    ) {
        parent::__construct();

        $currentProjects = [];
        /**
         * @var $projectService ProjectService
         */
        foreach ($projectService->findProjectByOrganisation(
            $organisation,
            ProjectService::WHICH_ALL
        ) as $project) {
            $currentProjects[] = $project->getId();
        }

        $projects = [];
        /**
         * @var $newProject Project
         */
        foreach ($projectService->findAllProjects(ProjectService::WHICH_ALL)->getResult() as $newProject) {
            if (! in_array($newProject->getId(), $currentProjects, true)) {
                $projects[$newProject->getId()] = sprintf('%s', $newProject);
            }
        }

        arsort($projects);

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->add(
            [
                'type'       => Select::class,
                'name'       => 'project',
                'options'    => [
                    'value_options' => $projects,
                    'help-block'    => _('txt-project-help-block'),
                ],
                'attributes' => [
                    'label' => _('txt-project'),
                ],
            ]
        );


        $this->add(
            [
                'type'       => Text::class,
                'name'       => 'branch',
                'options'    => [
                    'help-block' => _('txt-branch-help-block'),
                ],
                'attributes' => [
                    'label' => _('txt-branch'),
                ],
            ]
        );

        $contacts = [];
        foreach ($organisation->getContactOrganisation() as $contactOrganisation) {
            $contacts[$contactOrganisation->getContact()->getId()] = $contactOrganisation->getContact()->getFormName();
        }

        asort($contacts);

        $this->add(
            [
                'type'       => Contact::class,
                'name'       => 'contact',
                'options'    => [
                    'value_options' => $contacts,
                    'help-block'    => _('txt-technical-contact-help-block'),
                ],
                'attributes' => [
                    'label' => _('txt-technical-contact'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
                ],
            ]
        );
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );
    }
}
