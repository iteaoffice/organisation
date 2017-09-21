<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

declare(strict_types=1);

namespace Organisation\InputFilter;

use Organisation\Entity\Organisation;
use Zend\InputFilter\InputFilter;

/**
 * Class InvolvedSelect
 * @package SafetyForm\InputFilter
 */
class ManageWeb extends InputFilter
{
    /**
     * SubstrateSelect constructor.
     *
     * @param Organisation $organisation
     */
    public function __construct(Organisation $organisation)
    {
        $webInputFilter = new InputFilter();

        foreach ($organisation->getWeb() as $web) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'web',
                'required' => true,

            ]);

            $webInputFilter->add($inputFilter, $web->getId());
        }

        $this->add($webInputFilter, 'webFieldset');

        $this->add([
            'name'     => 'web',
            'required' => false,
        ]);
    }
}
