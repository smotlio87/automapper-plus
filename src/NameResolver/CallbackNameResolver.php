<?php

namespace AutoMapperPlus\NameResolver;

use AutoMapperPlus\Configuration\Options;
use AutoMapperPlus\MappingOperation\AlternativePropertyProvider;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;

/**
 * Class CallbackNameResolver
 *
 * @package AutoMapperPlus\NameResolver
 */
class CallbackNameResolver implements NameResolverInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * CallbackNameResolver constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function getSourcePropertyName
    (
        $targetPropertyName,
        MappingOperationInterface $operation,
        Options $options
    )
    {
        // We still allow properties to be overridden if explicitly defined
        if ($operation instanceof AlternativePropertyProvider) {
            return $operation->getAlternativePropertyName();
        }

        return call_user_func($this->callback, $targetPropertyName);
    }

}
