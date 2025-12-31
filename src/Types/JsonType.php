<?php
namespace Apie\Graphql\Types;

use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\ListValueNode;
use GraphQL\Language\AST\ObjectValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;

final class JsonType extends ScalarType
{
    public string $name = 'JSON';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return match (true) {
            $valueNode instanceof StringValueNode => $valueNode->value,
            $valueNode instanceof IntValueNode => (int) $valueNode->value,
            $valueNode instanceof FloatValueNode => (float) $valueNode->value,
            $valueNode instanceof BooleanValueNode => $valueNode->value,
            $valueNode instanceof ObjectValueNode => array_map(
                fn ($field) => $this->parseLiteral($field->value),
                iterator_to_array($valueNode->fields->getIterator())
            ),
            $valueNode instanceof ListValueNode => array_map(
                fn ($v) => $this->parseLiteral($v),
                iterator_to_array($valueNode->values->getIterator())
            ),
            default => null,
        };
    }
}
