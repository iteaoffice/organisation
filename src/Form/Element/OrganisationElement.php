<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Organisation\Form\Element;

use Laminas\Form\Element;
use Organisation\Entity;

/**
 * Class OrganisationElement
 * @package Organisation\Form\Element
 */
class OrganisationElement extends Element\Select
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setDisableInArrayValidator(true);
    }

    public function injectOrganisation(Entity\Organisation $organisation): void
    {
        $this->valueOptions[$organisation->getId()] = sprintf(
            '%s (%s)',
            $organisation->getOrganisation(),
            $organisation->getCountry()->getIso3()
        );
    }
}
