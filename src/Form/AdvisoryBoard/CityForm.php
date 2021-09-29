<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Form\AdvisoryBoard;

use Doctrine\ORM\EntityManager;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Organisation\Entity;
use Organisation\Form\ObjectFieldset;

/**
 *
 */
final class CityForm extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        $organisation = new Entity\AdvisoryBoard\City();
        parent::__construct($organisation->get('underscore_entity_name'));

        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $organisationFieldset = new ObjectFieldset($entityManager, $organisation);
        $organisationFieldset->setUseAsBaseFieldset(true);
        $this->add($organisationFieldset);

        $this->add(
            [
                'type' => Csrf::class,
                'name' => 'csrf',
            ]
        );

        $this->add(
            [
                'type'    => File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => 'txt-logo',
                    'help-block' => _('txt-organisation-logo-requirements'),
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
        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'delete',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-delete'),
                ],
            ]
        );
    }
}
