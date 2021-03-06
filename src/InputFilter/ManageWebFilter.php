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
use Organisation\Entity\Organisation;

/**
 * Class ManageWebFilter
 * @package Organisation\InputFilter
 */
final class ManageWebFilter extends InputFilter
{
    public function __construct(Organisation $organisation)
    {
        $webInputFilter = new InputFilter();

        foreach ($organisation->getWeb() as $web) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                [
                    'name'     => 'web',
                    'required' => true,

                ]
            );

            $webInputFilter->add($inputFilter, $web->getId());
        }

        $this->add($webInputFilter, 'webFieldset');

        $this->add(
            [
                'name'     => 'web',
                'required' => false,
            ]
        );
    }
}
