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
 * Class Parent
 *
 * @package Parent\Form\Element
 */
class ParentElement extends Element\Select
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setDisableInArrayValidator(true);
    }

    public function injectParent(Entity\ParentEntity $parent): void
    {
        $this->valueOptions[$parent->getId()] = sprintf(
            '%s (%s)',
            $parent->getOrganisation()->parseFullName(),
            $parent->getOrganisation()->getCountry()->getIso3()
        );
    }
}
