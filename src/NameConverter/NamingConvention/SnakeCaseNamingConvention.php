<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

/**
 * Class SnakeCaseNamingConvention
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
 */
class SnakeCaseNamingConvention extends BaseNamingConvention
{
    /**
     * @inheritdoc
     */
    public function toParts($name)
    {
        return explode('_', $name);
    }

    /**
     * @inheritdoc
     */
    public function fromParts(array $parts)
    {
        return implode('_', $parts);
    }

}
