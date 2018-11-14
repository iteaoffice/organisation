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

namespace Organisation\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Class OParentFilter
 *
 * @package Organisation\InputFilter
 */
class OParentFilter extends InputFilter
{
    /**
     * OParentFilter constructor.
     */
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
