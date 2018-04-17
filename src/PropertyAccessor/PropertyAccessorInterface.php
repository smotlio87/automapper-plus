<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Interface PropertyAccessorInterface
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
interface PropertyAccessorInterface
{
    /**
     * @param $object
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty($object, $propertyName);

    /**
     * @param $object
     * @param string $propertyName
     * @return mixed
     */
    public function getProperty($object, $propertyName);

    /**
     * @param $object
     * @param string $propertyName
     * @param $value
     */
    public function setProperty($object, $propertyName, $value);

    /**
     * Returns a list of property names available on the object.
     *
     * @param object $object
     * @return string[]
     */
    public function getPropertyNames($object);
}
