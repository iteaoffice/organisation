<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Organisation
 * @package     Entity
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Organisation\Entity;

interface EntityInterface
{
    public function __get($property);

    public function __set($property, $value);
}
