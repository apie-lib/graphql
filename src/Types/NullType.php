<?php
namespace Apie\Graphql\Types;

use GraphQL\Type\Definition\ScalarType;

final class NullType extends ScalarType
{
    public string $name = 'NULL';

    public function serialize($value)
    {
        return null;
    }

    public function parseValue($value)
    {
        return null;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return null;
    }
}
