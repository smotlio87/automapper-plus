<?php

namespace AutoMapperPlus\MappingOperation\Implementations;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

/**
 * Class Ignore
 *
 * @package AutoMapperPlus\MappingOperation\Implementations
 */
class Ignore implements MappingOperationInterface
{
    /**
     * @inheritdoc
     */
    public function mapProperty($propertyName, $source, $destination)
    {
        // Don't do anything.
    }

    /**
     * @inheritdoc
     */
    public function setOptions(Options $options)
    {
        // We don't need any configuration.
    }

}
