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

use Laminas\InputFilter\InputFilter;

/**
 * Class ParentEntityFilter
 *
 * @package Organisation\InputFilter
 */
final class ParentEntityFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            [
                'name'       => 'dateParentTypeUpdate',
                'required'   => false,
                'validators' => [
                    [
                        'name' => 'Date',
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

        $this->add($inputFilter, 'organisation_entity_parententity');
    }
}
