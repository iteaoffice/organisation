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
     * @return mixed|string
     */
    public function get($switch)
    {
        switch ($switch) {
            case 'entity_name':
                return implode('', array_slice(explode('\\', get_class($this)), -1));
            case 'dashed_entity_name':
                $dash = function ($m) {
                    return '-' . strtolower($m[1]);
                };

                return preg_replace_callback('/([A-Z])/', $dash, lcfirst($this->get('entity_name')));
            case 'underscore_entity_name':
                $underscore = function ($m) {
                    return '_' . strtolower($m[1]);
                };

                return preg_replace_callback('/([A-Z])/', $underscore, lcfirst($this->get('entity_name')));
            case 'underscore_full_entity_name':
                $underscore = function ($m) {
                    return '_' . strtolower($m[1]);
                };

                return preg_replace_callback(
                    '/([A-Z])/',
                    $underscore,
                    lcfirst(str_replace('\\', '', __NAMESPACE__) . $this->get('entity_name'))
                );
        }
    }
}
