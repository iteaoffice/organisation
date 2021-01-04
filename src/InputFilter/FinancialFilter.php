<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\UniqueObject;
use Organisation\Entity;
use Laminas\InputFilter\InputFilter;

/**
 * Class FinancialFilter
 *
 * @package Organisation\InputFilter
 */
final class FinancialFilter extends InputFilter
{
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            [
                'name'       => 'vat',
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Entity\Financial::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => ['vat'],
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'       => 'dateEnd',
                'required'   => false,
                'validators' => [
                    [
                        'name' => 'Date',
                    ],
                ],
            ]
        );

        $this->add($inputFilter, 'organisation_entity_financial');
    }
}
