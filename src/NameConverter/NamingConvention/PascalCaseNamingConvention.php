<?php

namespace AutoMapperPlus\NameConverter\NamingConvention;

use function Functional\map;

/**
 * Class PascalCaseNamingConvention
 *
 * @package AutoMapperPlus\NameConverter\NamingConvention
 */
class PascalCaseNamingConvention extends BaseNamingConvention
{
    /**
     * @inheritdoc
     */
    public function toParts($name)
    {
        $parts = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);

        return $this->normalize($parts);
    }

    /**
     * @inheritdoc
     */
    public function fromParts(array $parts)
    {
        $parts = map($parts, function ($part) {
            return ucfirst($part);
        });

        return implode('', $parts);
    }
}
