<?php
/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Class OParentFilter
 *
 * @package Organisation\InputFilter
 */
final class OParentFilter extends InputFilter
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

        $this->add($inputFilter, 'organisation_entity_oparent');
    }
}
