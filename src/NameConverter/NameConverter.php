<?php

namespace AutoMapperPlus\NameConverter;

use AutoMapperPlus\NameConverter\NamingConvention\NamingConventionInterface;

/**
 * Class NameConverter
 *
 * @package AutoMapperPlus\NameConverter
 */
class NameConverter implements NameConverterInterface
{
    /**
     * @inheritdoc
     */
    public static function convert
    (
        NamingConventionInterface $sourceNamingConvention,
        NamingConventionInterface $targetNamingConvention,
        $source
    )
    {
        $parts = $sourceNamingConvention->toParts($source);

        return $targetNamingConvention->fromParts($parts);
    }
}
