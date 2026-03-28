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

    public static function clear()
    {
        self::$json = null;
        self::$null = null;
        self::$createMeta = [];
        self::$modifyMeta = [];
        self::$methodCallMeta = [];
        self::$resultMeta = [];
        self::$created = [];
    }

    public static function getDefinedTypes(): array
    {
        $list = [
            ...self::$created,
        ];
        foreach (self::$createMeta as $meta) {
            $list[$meta->name] = $meta;
        }
        foreach (self::$modifyMeta as $meta) {
            $list[$meta->name] = $meta;
        }
        foreach (self::$methodCallMeta as $meta) {
            $list[$meta->name] = $meta;
        }
        foreach (self::$resultMeta as $meta) {
            $list[$meta->name] = $meta;
        }
        ksort($list);
        return $list;
    }

    private static function apieContext(): ApieContext
    {
        return new ApieContext([
            ContextConstants::GRAPHQL => 1,
        ]);
    }

    public static function createSingleton(string $typeName, callable $factory): Type
    {
        if (isset(self::$created[$typeName])) {
            return self::$created[$typeName];
        }
        self::$created[$typeName] = self::json();
        return self::$created[$typeName] = $factory(self::apieContext());
    }

    public static function createMeta(\ReflectionClass $class): FromMetadataInputType
    {
        if (isset(self::$createMeta[$class->name])) {
            return self::$createMeta[$class->name];
        }
        self::$createMeta[$class->name] = self::json();
        return self::$createMeta[$class->name] = new FromMetadataInputType(
            MetadataFactory::getCreationMetadata($class, self::apieContext()),
            '_create'
        );
    }

    public static function methodCallMeta(\ReflectionMethod $method): FromMetadataInputType
    {
        $key = $method->getDeclaringClass()->name . '::' . $method->name;
        if (isset(self::$methodCallMeta[$key])) {
            return self::$methodCallMeta[$key];
        }
        self::$methodCallMeta[$key] = self::json();
        return self::$methodCallMeta[$key] = new FromMetadataInputType(
            MetadataFactory::getMethodMetadata($method, self::apieContext()),
            'Run' . ucfirst($method->name)
        );
    }


    public static function modifyMeta(\ReflectionClass $class): FromMetadataInputType
    {
        if (isset(self::$modifyMeta[$class->name])) {
            return self::$modifyMeta[$class->name];
        }
        self::$modifyMeta[$class->name] = self::json();
        return self::$modifyMeta[$class->name] = new FromMetadataInputType(
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
        if (isset(self::$resultMeta[$class->name])) {
            return self::$resultMeta[$class->name];
        }

        self::$resultMeta[$class->name] = self::json();

        return self::$resultMeta[$class->name] = new FromMetadataType(
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
