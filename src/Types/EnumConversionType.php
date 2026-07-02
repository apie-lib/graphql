<?php
namespace Apie\Graphql\Types;

use Apie\Core\ValueObjects\Utils;
use Apie\TypeConverter\ReflectionTypeFactory;
use GraphQL\Type\Definition\EnumType;

class EnumConversionType extends EnumType
{
    public function parseValue($value): mixed
    {
        return Utils::toTypehint(
            ReflectionTypeFactory::createReflectionType($this->config['typehint']),
            $value
        );
    }
}
