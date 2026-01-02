<?php
namespace Apie\Graphql;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Graphql\Types\FromMetadataInputType;
use Apie\Graphql\Types\FromMetadataType;
use Apie\Graphql\Types\JsonType;
use Apie\Graphql\Types\NullType;

final class Types
{
    private static ?JsonType $json = null;
    private static ?NullType $null = null;

    private static array $createMeta = [];

    private static array $resultMeta = [];

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
    public static function createMeta(\ReflectionClass $class): FromMetadataInputType
    {
        return self::$createMeta[$class->name] ??= new FromMetadataInputType(
            MetadataFactory::getCreationMetadata($class, self::apieContext()),
            '_create'
        );
    }

    public static function displayMeta(\ReflectionClass $class): FromMetadataType
    {
        return self::$resultMeta[$class->name] ??= new FromMetadataType(
            MetadataFactory::getResultMetadata($class, self::apieContext())
        );
    }

    public static function json(): JsonType
    {
        return self::$json ??= new JsonType();
    }

    public static function null(): NullType
    {
        return self::$null ??= new NullType();
    }

}
