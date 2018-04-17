<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\Exception\NoConstructorSetException;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MapperInterface;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Operation;
use AutoMapperPlus\MappingOperation\Reversible;
use AutoMapperPlus\NameConverter\NameConverter;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;
use AutoMapperPlus\NameResolver\NameResolverInterface;
use AutoMapperPlus\PropertyAccessor\ObjectCratePropertyAccessor;
use function Functional\map;

/**
 * Class Mapping
 *
 * @package AutoMapperPlus\Configuration
 */
class Mapping implements MappingInterface
{
    /**
     * @var string
     */
    private $sourceClassName;

    /**
     * @var string
     */
    private $destinationClassName;

    /**
     * @var MappingOperationInterface[]
     */
    private $mappingOperations = [];

    /**
     * @var Options
     */
    private $options;

    /**
     * @var AutoMapperConfigInterface
     */
    private $autoMapperConfig;

    /**
     * @var callable
     */
    private $factoryCallback;

    /**
     * Mapping constructor.
     *
     * @param string $sourceClassName
     * @param string $destinationClassName
     * @param AutoMapperConfigInterface $autoMapperConfig
     */
    public function __construct
    (
        $sourceClassName,
        $destinationClassName,
        AutoMapperConfigInterface $autoMapperConfig
    )
    {
        $this->sourceClassName = $sourceClassName;
        $this->destinationClassName = $destinationClassName;
        $this->autoMapperConfig = $autoMapperConfig;

        // Inherit the options from the config.
        $this->options = clone $autoMapperConfig->getOptions();
        if ($this->options->shouldSkipConstructor()) {
            $this->skipConstructor();
        }

        // If this is a mapping that maps to an object crate, overwrite the
        // property accessor in the options.
        if ($this->options->isObjectCrate($this->destinationClassName)) {
            $this->options->setPropertyAccessor(new ObjectCratePropertyAccessor());
        }
    }

    /**
     * @inheritdoc
     */
    public function getSourceClassName()
    {
        return $this->sourceClassName;
    }

    /**
     * @inheritdoc
     */
    public function getDestinationClassName()
    {
        return $this->destinationClassName;
    }

    /**
     * @inheritdoc
     */
    public function forMember
    (
        $targetPropertyName,
        $operation
    )
    {
        // If it's just a regular callback, wrap it in an operation.
        if (!$operation instanceof MappingOperationInterface) {
            $operation = Operation::mapFrom($operation);
        }

        // Make the config available to the operation.
        $operation->setOptions($this->options);

        $this->mappingOperations[$targetPropertyName] = $operation;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reverseMap(array $options = [])
    {
        $reverseMapping = $this->autoMapperConfig->registerMapping(
            $this->getDestinationClassName(),
            $this->getSourceClassName()
        );

        // If there are any naming conventions set, we should reverse those as
        // well for the new mapping.
        if ($this->options->shouldConvertName()) {
            $reverseMapping->withNamingConventions(
                $this->options->getDestinationMemberNamingConvention(),
                $this->options->getSourceMemberNamingConvention()
            );
        }

        // Check if we can reverse any operations for the new mapping.
        foreach ($this->mappingOperations as $originalProperty => $mappingOperation) {
            // We can only define the reverse operation for operations
            // implementing Reversible.
            if (!$mappingOperation instanceof Reversible) {
                continue;
            }

            $reverseTargetProperty = $mappingOperation->getReverseTargetPropertyName(
                $originalProperty,
                $this->getOptions()
            );
            $reverseOperation = $mappingOperation->getReverseOperation(
                $originalProperty,
                $reverseMapping->getOptions()
            );
            $reverseMapping->forMember($reverseTargetProperty, $reverseOperation);
        }

        return $reverseMapping;
    }

    /**
     * @inheritdoc
     */
    public function copyFrom
    (
        $sourceClass,
        $destinationClass
    )
    {
        $mapping = $this->autoMapperConfig->getMappingFor(
            $sourceClass,
            $destinationClass
        );
        if (!$mapping) {
            $message = "Can't copy a mapping that isn't registered yet.";
            throw new UnregisteredMappingException($message);
        }

        return $this->copyFromMapping($mapping);
    }

    /**
     * @inheritdoc
     */
    public function copyFromMapping(MappingInterface $mapping)
    {
        $this->mappingOperations = $mapping->getRegisteredMappingOperations();
        $this->options = clone $mapping->getOptions();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @todo: move this logic to a separate class.
     */
    public function getTargetProperties($targetObject, $sourceObject)
    {
        // We use the property accessor defined on the config, because the one
        // in this mapping's Options might have been overridden to be the
        // object crate implementation.
        $propertyAccessor = $this->autoMapperConfig->getOptions()->getPropertyAccessor();
        if (!$this->options->isObjectCrate($this->destinationClassName)) {
            $properties = $propertyAccessor->getPropertyNames($targetObject);
            return array_values($properties);
        }

        $sourceProperties = $propertyAccessor->getPropertyNames($sourceObject);
        $sourceProperties = array_values($sourceProperties);
        if (!$this->options->shouldConvertName()) {
            return $sourceProperties;
        }

        $sourceNamingConvention = $this->options->getSourceMemberNamingConvention();
        $destinationNamingConvention = $this->options->getDestinationMemberNamingConvention();
        $nameConverter = function ($sourceProperty) use (
            $sourceNamingConvention,
            $destinationNamingConvention
        ) {
            return NameConverter::convert(
                $this->options->getSourceMemberNamingConvention(),
                $this->options->getDestinationMemberNamingConvention(),
                $sourceProperty
            );
        };

        return map($sourceProperties, $nameConverter);
    }

    /**
     * @inheritdoc
     */
    public function beConstructedUsing
    (
        callable $factoryCallback
    )
    {
        $this->factoryCallback = $factoryCallback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomConstructor()
    {
        if (empty($this->factoryCallback)) {
            throw NoConstructorSetException::fromMapping($this);
        }

        return $this->factoryCallback;
    }

    /**
     * @inheritdoc
     */
    public function hasCustomConstructor()
    {
        return !empty($this->factoryCallback);
    }

    /**
     * @inheritdoc
     */
    public function getRegisteredMappingOperations()
    {
        return $this->mappingOperations;
    }

    /**
     * @inheritdoc
     */
    public function getMappingOperationFor($propertyName)
    {
        return isset($this->mappingOperations[$propertyName]) ? $this->mappingOperations[$propertyName] : $this->getDefaultMappingOperation();
    }

    /**
     * @inheritdoc
     */
    public function setDefaults(callable $configurator)
    {
        $configurator($this->options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function skipConstructor()
    {
        return $this->beConstructedUsing(function () {
            $reflectionClass = new \ReflectionClass($this->destinationClassName);
            return $reflectionClass->newInstanceWithoutConstructor();
        });
    }

    /**
     * @inheritdoc
     */
    public function dontSkipConstructor()
    {
        $this->factoryCallback = null;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withNamingConventions
    (
        NamingConventionInterface $sourceNamingConvention,
        NamingConventionInterface $destinationNamingConvention
    )
    {
        $this->options->setSourceMemberNamingConvention($sourceNamingConvention);
        $this->options->setDestinationMemberNamingConvention($destinationNamingConvention);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withDefaultOperation
    (
        MappingOperationInterface $mappingOperation
    )
    {
        $this->options->setDefaultMappingOperation($mappingOperation);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withNameResolver
    (
        NameResolverInterface $nameResolver
    )
    {
        $this->options->setNameResolver($nameResolver);

        return $this;
    }

    /**
     * @return MappingOperationInterface
     */
    protected function getDefaultMappingOperation()
    {
        $operation = $this->options->getDefaultMappingOperation();
        $operation->setOptions($this->options);

        return $operation;
    }

    /**
     * @inheritdoc
     */
    public function useCustomMapper(MapperInterface $mapper)
    {
        $this->options->setCustomMapper($mapper);
    }

    /**
     * @inheritdoc
     */
    public function providesCustomMapper()
    {
        return $this->options->providesCustomMapper();
    }

    /**
     * @inheritdoc
     */
    public function getCustomMapper()
    {
        return $this->options->getCustomMapper();
    }
}
