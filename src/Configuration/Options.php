<?php

namespace AutoMapperPlus\Configuration;

use AutoMapperPlus\MapperInterface;
use AutoMapperPlus\MappingOperation\DefaultMappingOperation;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;
use AutoMapperPlus\NameResolver\NameResolver;
use AutoMapperPlus\NameResolver\NameResolverInterface;
use AutoMapperPlus\PropertyAccessor\PropertyAccessor;
use AutoMapperPlus\PropertyAccessor\PropertyAccessorInterface;

/**
 * Class Options
 *
 * @package AutoMapperPlus\Configuration
 */
class Options
{
    /**
     * @var NamingConventionInterface|null
     */
    private $sourceMemberNamingConvention;

    /**
     * @var NamingConventionInterface|null
     */
    private $destinationMemberNamingConvention;

    /**
     * @var bool
     */
    private $shouldSkipConstructor = false;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var NameResolverInterface
     */
    private $nameResolver;

    /**
     * @var MappingOperationInterface
     */
    private $defaultMappingOperation;

    /**
     * @var MapperInterface|null
     */
    private $customMapper;

    /**
     * @var string
     */
    private $objectCrates = [];

    /**
     * @return Options
     *
     * Note: the skipConstructor default will be replaced by dontSkipConstructor
     *       in the next major release.
     */
    public static function defaults()
    {
        $options = new static;
        $options->skipConstructor();
        $options->setPropertyAccessor(new PropertyAccessor());
        $options->setDefaultMappingOperation(new DefaultMappingOperation());
        $options->setNameResolver(new NameResolver());
        $options->registerObjectCrate(\stdClass::class);

        return $options;
    }

    /**
     * @return NamingConventionInterface|null
     */
    public function getSourceMemberNamingConvention()
    {
        return $this->sourceMemberNamingConvention;
    }

    /**
     * @param NamingConventionInterface $sourceMemberNamingConvention
     */
    public function setSourceMemberNamingConvention
    (
        NamingConventionInterface $sourceMemberNamingConvention
    )
    {
        $this->sourceMemberNamingConvention = $sourceMemberNamingConvention;
    }

    /**
     * @return NamingConventionInterface|null
     */
    public function getDestinationMemberNamingConvention()
    {
        return $this->destinationMemberNamingConvention;
    }

    /**
     * @param NamingConventionInterface $destinationMemberNamingConvention
     */
    public function setDestinationMemberNamingConvention
    (
        NamingConventionInterface $destinationMemberNamingConvention
    )
    {
        $this->destinationMemberNamingConvention = $destinationMemberNamingConvention;
    }

    /**
     * @return bool
     */
    public function shouldSkipConstructor()
    {
        return $this->shouldSkipConstructor;
    }

    public function skipConstructor()
    {
        $this->shouldSkipConstructor = true;
    }

    public function dontSkipConstructor()
    {
        $this->shouldSkipConstructor = false;
    }

    /**
     * @param bool $shouldSkipConstructor
     */
    public function setShouldSkipConstructor($shouldSkipConstructor)
    {
        $this->shouldSkipConstructor = $shouldSkipConstructor;
    }

    /**
     * @return PropertyAccessorInterface
     */
    public function getPropertyAccessor()
    {
        return $this->propertyAccessor;
    }

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function setPropertyAccessor
    (
        PropertyAccessorInterface $propertyAccessor
    )
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @return MappingOperationInterface
     */
    public function getDefaultMappingOperation()
    {
        return $this->defaultMappingOperation;
    }

    /**
     * @param MappingOperationInterface $defaultMappingOperation
     */
    public function setDefaultMappingOperation
    (
        MappingOperationInterface $defaultMappingOperation
    )
    {
        $this->defaultMappingOperation = $defaultMappingOperation;
    }

    /**
     * @return NameResolverInterface
     */
    public function getNameResolver()
    {
        return $this->nameResolver;
    }

    /**
     * @param NameResolverInterface $nameResolver
     */
    public function setNameResolver(NameResolverInterface $nameResolver)
    {
        $this->nameResolver = $nameResolver;
    }

    /**
     * Whether or not property names should be converted between source and
     * destination.
     *
     * @return bool
     */
    public function shouldConvertName()
    {
        return !empty($this->sourceMemberNamingConvention)
            && !empty($this->destinationMemberNamingConvention);
    }

    /**
     * @return MapperInterface|null
     */
    public function getCustomMapper()
    {
        return $this->customMapper;
    }

    /**
     * @param MapperInterface $customMapper
     */
    public function setCustomMapper(MapperInterface $customMapper)
    {
        $this->customMapper = $customMapper;
    }

    /**
     * @return bool
     */
    public function providesCustomMapper()
    {
        return !empty($this->customMapper);
    }

    /**
     * @param string $className
     */
    public function registerObjectCrate($className)
    {
        $this->objectCrates[$className] = true;
    }

    /**
     * @param string $className
     */
    public function removeObjectCrate($className)
    {
        unset($this->objectCrates[$className]);
    }

    /**
     * @param string $className
     * @return bool
     */
    public function isObjectCrate($className)
    {
        return isset($this->objectCrates[$className]);
    }
}
