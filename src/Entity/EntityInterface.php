<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Organisation\Entity;

/**
 * Interface EntityInterface
 *
 * @package Organisation\Entity
 */
interface EntityInterface
{
    /**
     * @param $property
     *
     * @return mixed
     */
    public function __get($property);

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function __set($property, $value);

    /**
     * @param $property
     *
     * @return mixed
     */
    public function __isset($property);

    /**
     * @return int
     */
    public function getId();
}
