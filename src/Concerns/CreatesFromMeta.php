<?php
namespace Apie\Graphql\Concerns;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Graphql\Types;
use GraphQL\Type\Definition\Type;

trait CreatesFromMeta
{
    public static function createFromMetadata(MetadataInterface $metadata, bool $nullable = false): Type
    {
        $scalarType = $metadata->toScalarType($nullable);
        if ($scalarType === ScalarType::STDCLASS) {
            $result = new self($metadata);
            if ($nullable) {
                return $result;
            }
            return Type::nonNull($result);
        }
        return self::createFromScalar($scalarType, $nullable);
    }

    public static function createFromScalar(ScalarType $scalarType, bool $nullable = false): Type
    {
        $result = match($scalarType) {
            ScalarType::STRING => Type::string(),
            ScalarType::INTEGER => Type::int(),
            ScalarType::FLOAT => Type::float(),
            ScalarType::BOOLEAN => Type::boolean(),
            ScalarType::NULLVALUE => Types::null(),
            ScalarType::ARRAY => Type::listOf(Types::json()),
            ScalarType::STDCLASS => Types::json(),
            ScalarType::MIXED => Types::json(),
        };
        if ($nullable) {
            return $result;
        }
        return Type::nonNull($result);
    }

    public static function createFromField(FieldInterface $fieldMetadata): Type
    {
        $scalar = ScalarType::createFromReflectionType($fieldMetadata->getTypehint(), true);
        $nullable = $fieldMetadata->allowsNull() || !$fieldMetadata->isRequired();
        return self::createFromScalar($scalar, $nullable);
    }
}
