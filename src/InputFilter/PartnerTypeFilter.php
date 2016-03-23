<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

namespace Partner\InputFilter;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator;
use Partner\Entity\PartnerType;
use Zend\InputFilter\InputFilter;

/**
 * Jield webdev copyright message placeholder.
 *
 * @category    Partner
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2015 Jield (http://jield.nl)
 */
class PartnerTypeFilter extends InputFilter
{
    /**
     * PartnerFilter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name'       => 'type',
            'required'   => true,
            'validators' => [
                [
                    'name'    => Validator\UniqueObject::class,
                    'options' => [
                        'object_repository' => $entityManager->getRepository(PartnerType::class),
                        'object_manager'    => $entityManager,
                        'use_context'       => true,
                        'fields'            => 'type',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'description',
            'required' => true,
        ]);

        $this->add($inputFilter, PartnerType::class);
    }
}
