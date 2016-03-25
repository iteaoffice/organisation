<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Organisation\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Annotations class.
 *
 * @author  Johan van der Heide <johan.van.der.heide@itea3.org>
 */
abstract class EntityAbstract implements EntityInterface
{
    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->getId());
    }

    /**
     * Returns the string identifier of the Resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return sprintf("%s:%s", static::class, $this->getId());
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    }

    /**
     * @param $prop
     *
     * @return bool
     */
    public function has($prop)
    {
        $getter = 'get' . ucfirst($prop);
        if (method_exists($this, $getter)) {
            if ('s' === substr($prop, 0, -1) && is_array($this->$getter())) {
                return true;
            } elseif ($this->$getter()) {
                return true;
            }
        }
    }

    /**
     * @param $switch
     *
     * @return null|string
     */
    public function get($switch)
    {
        switch ($switch) {
            case 'full_entity_name':
                return static::class;
            case 'entity_name':
                return str_replace(__NAMESPACE__ . '\\', '', static::class);
        }

        return null;
    }
}
