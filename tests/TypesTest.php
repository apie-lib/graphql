<?php
namespace Apie\Tests\Graphql;

use Apie\Graphql\ExampleClass;
use Apie\Graphql\Types;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TypesTest extends TestCase
{
    #[Test]
    public function types_factory_always_gives_same_instance_for_json(): void
    {
        $firstInstance = Types::json();
        $secondInstance = Types::json();
        $this->assertSame($firstInstance, $secondInstance);
    }

    #[Test]
    public function types_factory_always_gives_same_instance_for_null(): void
    {
        $firstInstance = Types::null();
        $secondInstance = Types::null();
        $this->assertSame($firstInstance, $secondInstance);
    }
}
