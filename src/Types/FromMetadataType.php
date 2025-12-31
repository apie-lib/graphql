<?php
namespace Apie\Graphql\Types;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Graphql\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class FromMetadataType extends ObjectType
{
    public function __construct(private readonly MetadataInterface $metadata)
    {
        $config = [
            'name' => $this->metadata->toClass()?->getShortName() ?? $metadata->toScalarType()->value,
            'fields' => [
            ],
        ];
        foreach ($metadata->getHashmap() as $name => $field) {
            if ($field->isField()) {
                $config['fields'][$name] = [
                    'type' => self::createFromField($field),
                ];
            }
        }
        parent::__construct($config);
    }

    public static function createFromMetadata(MetadataInterface $metadata, bool $nullable = false): Type
    {
        $scalarType = $metadata->toScalarType();
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
        $nullable = $fieldMetadata->allowsNull();
        return self::createFromScalar($scalar, $nullable);
    }
}
