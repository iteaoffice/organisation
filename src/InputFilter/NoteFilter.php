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

use Laminas\InputFilter\InputFilter;

/**
 * Class NoteFilter
 *
 * @package Organisation\InputFilter
 */
final class NoteFilter extends InputFilter
{
    public function __construct()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add(
            [
                'name'       => 'source',
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
                'name'     => 'note',
                'required' => true,
            ]
        );

        $this->add($inputFilter, 'organisation_entity_note');
    }
}
