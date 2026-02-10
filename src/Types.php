<?php
namespace Apie\Graphql;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Graphql\Types\FromMetadataInputType;
use Apie\Graphql\Types\FromMetadataType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\Type;
use MLL\GraphQLScalars\MixedScalar;
use MLL\GraphQLScalars\NullScalar;

final class Types
{
    private static ?MixedScalar $json = null;
    private static ?NullScalar $null = null;

    private static array $createMeta = [];

    private static array $modifyMeta = [];

    private static array $methodCallMeta = [];

    private static array $resultMeta = [];

    private static array $created = [];

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    private static function apieContext(): ApieContext
    {
        return new ApieContext([
            ContextConstants::GRAPHQL => 1,
        ]);
    }

    public static function createSingleton(string $typeName, callable $factory): Type
    {
        return self::$created[$typeName] ??= $factory(self::apieContext());
    }

    public static function createMeta(\ReflectionClass $class): FromMetadataInputType
    {
        return self::$createMeta[$class->name] ??= new FromMetadataInputType(
            MetadataFactory::getCreationMetadata($class, self::apieContext()),
            '_create'
        );
    }

    public static function methodCallMeta(\ReflectionMethod $method): FromMetadataInputType
    {
        $key = $method->getDeclaringClass()->name . '::' . $method->name;
        return self::$methodCallMeta[$key] ??= new FromMetadataInputType(
            MetadataFactory::getMethodMetadata($method, self::apieContext()),
            'Run' . ucfirst($method->name)
        );
    }


    public static function modifyMeta(\ReflectionClass $class): FromMetadataInputType
    {
        return self::$modifyMeta[$class->name] ??= new FromMetadataInputType(
            MetadataFactory::getModificationMetadata($class, self::apieContext()),
            '_modify'
        );
    }

    public static function fromId(\ReflectionClass $class): Type
    {
        $meta = MetadataFactory::getResultMetadata($class, self::apieContext());
        $type = FromMetadataType::createFromField($meta->getHashmap()['id']);
        if ($type instanceof NonNull) {
            return $type->getWrappedType();
        }
        return $type;
    }

    public static function displayMeta(\ReflectionClass $class): FromMetadataType
    {
        return self::$resultMeta[$class->name] ??= new FromMetadataType(
            MetadataFactory::getResultMetadata($class, self::apieContext())
        );
    }

    public static function json(): MixedScalar
    {
        return self::$json ??= new MixedScalar();
    }

    public static function null(): NullScalar
    {
        return self::$null ??= new NullScalar();
    }

}
