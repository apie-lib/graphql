<?php declare(strict_types=1);

namespace Apie\Graphql\Types;

use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\WrappingType;
use GraphQL\Type\Schema;

/**
 * Graphql mapping for ItemHashmap.
 */
class MapOfType extends ScalarType implements InputType, OutputType, NullableType
{
    /**
     * @var Type|callable
     *
     * @phpstan-var OfType|callable(): OfType
     */
    private $wrappedType;

    /**
     * @param Type|callable $type
     *
     * @phpstan-param OfType|callable(): OfType $type
     */
    public function __construct($type)
    {
        $this->wrappedType = $type;
        $named = Type::getNamedType($type);
        parent::__construct([
            'name' => 'MapOf' . $named->name,
            'description' => 'A map/dictionary/hashmap where the keys are strings and the values are of type ' . $named->name,
        ]);
    }

    public function serialize($value): mixed
    {
        return $value;
    }

    public function parseValue($value): mixed
    {
        if (!is_iterable($value)) {
            throw new InvariantViolation('Could not parse value as MapOfType, value is not iterable.');
        }
        $res = [];
        foreach ($value as $key => $item) {
            $res[$key] = $this->getWrappedType()->parseValue($item);
        }
        return $res;
    }

    public function parseLiteral($valueNode, array $variables = null): mixed
    {
        return $valueNode->value;
    }

    /** @phpstan-return OfType */
    public function getWrappedType(): Type
    {
        return Schema::resolveType($this->wrappedType);
    }

    public function getInnermostType(): NamedType
    {
        $type = $this->getWrappedType();
        while ($type instanceof WrappingType) {
            $type = $type->getWrappedType();
        }

        assert($type instanceof NamedType, 'known because we unwrapped all the way down');

        return $type;
    }
}
