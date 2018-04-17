<?php

namespace AutoMapperPlus\PropertyAccessor;

/**
 * Class ObjectCratePropertyAccessor
 *
 * Object crates are objects like stdClass, who just accept any properties you
 * try to write to them (see #3).
 *
 * @package AutoMapperPlus\PropertyAccessor
 */
class ObjectCratePropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @inheritdoc
     */
    public function hasProperty($object, $propertyName)
    {
        // We'll assume an object crate always has the desired property.
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getProperty($object, $propertyName)
    {
        return $object->{$propertyName};
    }

    /**
     * @inheritdoc
     */
    public function setProperty($object, $propertyName, $value)
    {
        $object->{$propertyName} = $value;
    }

    /**
     * @inheritdoc
     */
    public function getPropertyNames($object)
    {
        // We could alternatively throw an error here, need some more thought.
        return [];
    }
}
