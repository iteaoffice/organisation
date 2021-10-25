<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\InputFilter\AdvisoryBoard;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\Size;
use Organisation\Entity\AdvisoryBoard\City;

/**
 *
 */
final class CityFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            [
                'name'       => 'name',
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ],
                    ],
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(City::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'name',
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'       => 'tenderWebsite',
                'required'   => false,
            ]
        );

        $inputFilter->add(
            [
                'name'       => 'file',
                'required'   => false,
                'validators' => [
                    new Size(
                        [
                            'min' => '1kB',
                            'max' => '16MB',
                        ]
                    ),
                    new IsImage(),
                ],
            ]
        );

        $this->add($inputFilter, 'organisation_entity_advisoryboard_city');
    }
}
