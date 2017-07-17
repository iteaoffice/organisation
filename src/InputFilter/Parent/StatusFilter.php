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
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\InputFilter\Parent;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Organisation\Entity\Parent\Status;
use Zend\InputFilter\InputFilter;

/**
 * Class StatusFilter
 *
 * @package Organisation\InputFilter\Parent
 */
class StatusFilter extends InputFilter
{
    /**
     * PartnerFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(
            [
                'name'       => 'status',
                'required'   => true,
                'validators' => [
                    [
                        'name'    => Validator\UniqueObject::class,
                        'options' => [
                            'object_repository' => $entityManager->getRepository(Status::class),
                            'object_manager'    => $entityManager,
                            'use_context'       => true,
                            'fields'            => 'status',
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

        $this->add($inputFilter, 'organisation_entity_parent_status');
    }
}
