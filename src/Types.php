<?php
namespace Apie\Graphql;

use Apie\Graphql\Types\JsonType;
use Apie\Graphql\Types\NullType;

final class Types
{
    private static ?JsonType $json = null;
    private static ?NullType $null = null;

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
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
