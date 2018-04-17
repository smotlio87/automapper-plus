<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;

/**
 * Class DefaultMappingOperation
 *
 * @package AutoMapperPlus\MappingOperation
 */
class DefaultMappingOperation implements MappingOperationInterface
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @inheritdoc
     */
    public function mapProperty($propertyName, $source, $destination)
    {
        if (!$this->canMapProperty($propertyName, $source)) {
            // Alternatively throw an error here.
            return;
        }
        $sourceValue = $this->getSourceValue($source, $propertyName);
        $this->setDestinationValue($destination, $propertyName, $sourceValue);
    }

    /**
     * @inheritdoc
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $propertyName
     * @param $source
     * @return bool
     */
    protected function canMapProperty($propertyName, $source)
    {
        $sourcePropertyName = $this->getSourcePropertyName($propertyName);

        return $this->getPropertyAccessor()->hasProperty($source, $sourcePropertyName);
    }

    /**
     * @param $source
     * @param string $propertyName
     * @return mixed
     */
    protected function getSourceValue($source, $propertyName)
    {
        return $this->getPropertyAccessor()->getProperty(
            $source,
            $this->getSourcePropertyName($propertyName)
        );
    }

    /**
     * @param $destination
     * @param string $propertyName
     * @param $value
     */
    protected function setDestinationValue($destination, $propertyName, $value)
    {
        $this->getPropertyAccessor()->setProperty($destination, $propertyName, $value);
    }

    /**
     * @return PropertyAccessorInterface
     */
    protected function getPropertyAccessor()
    {
        return $this->options->getPropertyAccessor();
    }

    /**
     * Returns the name of the property we should fetch from the source object.
     *
     * @param string $propertyName
     * @return string
     */
    protected function getSourcePropertyName($propertyName)
    {
        return $this->options->getNameResolver()->getSourcePropertyName(
            $propertyName,
            $this,
            $this->options
        );
    }
}
