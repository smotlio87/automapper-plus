<?php

namespace AutoMapperPlus\Configuration;

/**
 * Interface AutoMapperConfigInterface
 *
 * @package AutoMapperPlus\Configuration
 */
interface AutoMapperConfigInterface
{
    /**
     * Checks if a mapping exists between the given classes.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return bool
     */
    public function hasMappingFor(
        $sourceClassName,
        $destinationClassName
    );

    /**
     * Retrieves the mapping for the given classes.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return MappingInterface|null
     */
    public function getMappingFor(
        $sourceClassName,
        $destinationClassName
    );

    /**
     * Register a mapping between two classes. Without any additional
     * configuration, this will perform the default operation for every
     * property.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @return MappingInterface
     */
    public function registerMapping(
        $sourceClassName,
        $destinationClassName
    );

    /**
     * @return Options
     */
    public function getOptions();
}
