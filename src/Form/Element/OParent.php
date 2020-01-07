<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Parent
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Form\Element;

use Organisation\Entity;
use Laminas\Form\Element;

/**
 * Class Parent
 *
 * @package Parent\Form\Element
 */
class OParent extends Element\Select
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setDisableInArrayValidator(true);
    }

    public function injectParent(Entity\OParent $parent): void
    {
        $this->valueOptions[$parent->getId()] = sprintf(
            '%s (%s)',
            $parent->getOrganisation()->parseFullName(),
            $parent->getOrganisation()->getCountry()->getIso3()
        );
    }
}
