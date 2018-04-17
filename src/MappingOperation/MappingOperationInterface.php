<?php

namespace AutoMapperPlus\MappingOperation;

use AutoMapperPlus\Configuration\Options;

/**
 * Interface MappingOperationInterface
 *
 * @package AutoMapperPlus\MappingOperation
 */
interface MappingOperationInterface
{
    /**
     * @param string $propertyName
     * @param $source
     * @param $destination
     * @return void
     */
    public function mapProperty($propertyName, $source, $destination);

    /**
     * @param Options $options
     */
    public function setOptions(Options $options);
}
