<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\InputFilter\Parent;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Organisation\Entity\Parent\Type;
use Zend\InputFilter\InputFilter;

/**
 * Class TypeFilter
 *
 * @package Organisation\InputFilter\Parent
 */
class TypeFilter extends InputFilter
{
    /**
     * ParentFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'type',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Validator\UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Type::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'type',
                        ],
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'description',
                'required' => true,
            ]
        );

        $this->add($inputFilter, 'organisation_entity_parent_type');
    }
}
