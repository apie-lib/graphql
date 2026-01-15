<?php
namespace Apie\Graphql\Concerns;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\ItemHashmapMetadata;
use Apie\Core\Metadata\ItemListMetadata;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\Metadata\NullableMetadataInterface;
use Apie\Core\Metadata\UnionTypeMetadata;
use Apie\Core\Utils\ConverterUtils;
use Apie\Graphql\Types;
use Apie\Graphql\Types\FromMetadataInputType;
use Apie\Graphql\Types\MapOfType;
use Apie\TypeConverter\ReflectionTypeFactory;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;

trait CreatesFromMeta
{
    public static function createFromMetadata(MetadataInterface $metadata, bool $nullable = false): Type
    {
        if ($metadata instanceof NullableMetadataInterface) {
            $nullable = $nullable || $metadata->allowsNull();
        }
        if ($metadata instanceof UnionTypeMetadata) {
            $metaWithoutNull = $metadata->toSkipNull();
            
            if ($metaWithoutNull instanceof UnionTypeMetadata && ScalarType::MIXED === $metaWithoutNull->toScalarType()) {
                $name = 'UnionOf' . md5(static::class . $metadata->getDisplayName());
                $result = Types::createSingleton(
                    $name,
                    fn() => new UnionType([
                        'name' => $name,
                        'types' => array_map(
                            fn(MetadataInterface $meta) => self::createFromMetadata($meta),
                            $metaWithoutNull->getTypes()
                        ),
                    ])
                );
                if ($nullable) {
                    return $result;
                }
                return Type::nonNull($result);
            }
           $metadata = $metaWithoutNull;
        }


        if ($metadata instanceof ItemListMetadata) {
            $result = Type::listOf(self::createFromMetadata($metadata->getArrayItemType()));
            if ($nullable) {
                return $result;
            }
            return Type::nonNull($result);
        }
        if ($metadata instanceof ItemHashmapMetadata) {
            $result = new MapOfType(self::createFromMetadata($metadata->getArrayItemType()));
            if ($nullable) {
                return $result;
            }
            return Type::nonNull($result);
        }
        
        
        $scalarType = $metadata->toScalarType($nullable);
        if ($scalarType === ScalarType::STDCLASS) {
            $result = new self($metadata);
            $result = Types::createSingleton(
                $result->name,
                fn() => $result
            );
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
            ScalarType::STDCLASS => new MapOfType(Types::json()),
            ScalarType::MIXED => Types::json(),
        };
        if ($nullable) {
            return $result;
        }
        return Type::nonNull($result);
    }

    public static function createFromField(FieldInterface $fieldMetadata): Type
    {
        $type = $fieldMetadata->getTypehint();
        $class = ConverterUtils::toReflectionClass($type);
        $nullable = $fieldMetadata->allowsNull() || !$fieldMetadata->isRequired();
        $scalar = ScalarType::createFromReflectionType($type, $nullable);
        
        if ($class !== null && in_array($scalar, [ScalarType::STDCLASS, ScalarType::MIXED], true)) {
            $method = static::class === FromMetadataInputType::class ? 'getCreationMetadata' : 'getResultMetadata';
            return self::createFromMetadata(
                MetadataFactory::getMetadataStrategyForType(
                    $type ?? ReflectionTypeFactory::createReflectionType('mixed')
                )->$method(new ApieContext()),
                $nullable
            );
        }
        
        return self::createFromScalar($scalar, $nullable);
    }
}
