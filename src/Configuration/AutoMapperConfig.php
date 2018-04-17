<?php

namespace AutoMapperPlus\Configuration;

use function Functional\first;

/**
 * Class AutoMapperConfig
 *
 * @package AutoMapperPlus\Configuration
 */
class AutoMapperConfig implements AutoMapperConfigInterface
{
    /**
     * @var MappingInterface[]
     */
    private $mappings = [];

    /**
     * @var Options
     */
    private $options;

    /**
     * AutoMapperConfig constructor.
     *
     * @param callable $configurator
     */
    function __construct(callable $configurator = null)
    {
        $this->options = Options::defaults();
        if ($configurator) {
            $configurator($this->options);
        }
    }

    /**
     * @inheritdoc
     */
    public function hasMappingFor
    (
        $sourceClassName,
        $destinationClassName
    )
    {
        return !empty($this->getMappingFor($sourceClassName, $destinationClassName));
    }

    /**
     * @inheritdoc
     */
    public function getMappingFor
    (
        $sourceClassName,
        $destinationClassName
    )
    {
        return first(
            $this->mappings,
            function (MappingInterface $mapping) use ($sourceClassName, $destinationClassName) {
                return $mapping->getSourceClassName() == $sourceClassName
                    && $mapping->getDestinationClassName() == $destinationClassName;
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function registerMapping
    (
        $sourceClassName,
        $destinationClassName
    )
    {
        $mapping = new Mapping(
            $sourceClassName,
            $destinationClassName,
            $this
        );
        $this->mappings[] = $mapping;

        return $mapping;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }
}
