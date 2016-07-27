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

namespace Organisation\InputFilter;

use Doctrine\ORM\EntityManager;
use Organisation\Entity;
use Zend\InputFilter\InputFilter;

/**
 * Class FinancialFilter
 *
 * @package Organisation\InputFilter
 */
class FinancialFilter extends InputFilter
{
    /**
     * FinancialFilter constructor.
     *
     * @param EntityManager $entityManager
     */
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
                        'name'    => '\DoctrineModule\Validator\UniqueObject',
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
