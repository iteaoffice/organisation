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

namespace Organisation\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Class OrganisationFilter
 * @package Organisation\InputFilter
 */
class OrganisationFilter extends InputFilter
{
    /**
     * OrganisationFilter constructor.
     */
    public function __construct()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            [
                'name'       => 'organisation',
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
                ],
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'country',
                'required' => true,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'type',
                'required' => true,
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'domain',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'technology',
                'required' => false,
            ]
        );
        $inputFilter->add(
            [
                'name'     => 'clusterMember',
                'required' => false,
            ]
        );


        $this->add($inputFilter, 'organisation_entity_organisation');
    }
}
