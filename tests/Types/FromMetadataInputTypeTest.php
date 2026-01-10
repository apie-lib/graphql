<?php
namespace Apie\Tests\Graphql\Factories;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\ItemSet;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\ValueObjects\IsStringValueObjectExample;
use Apie\Graphql\Types;
use Apie\Graphql\Types\FromMetadataInputType;
use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FromMetadataInputTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('metadataProvider')]
    public function it_can_create_a_type_input_definition(
        Type $expected,
        string $typehint,
        string $metadataMethod,
        bool $nullable
    ) {
        $metadata = MetadataFactory::$metadataMethod(
            ReflectionTypeFactory::createReflectionType($typehint),
            new ApieContext([])
        );
        $actual = FromMetadataInputType::createFromMetadata($metadata, $nullable);
        $this->assertEquals($expected, $actual);
    }

    public static function metadataProvider(): Generator
    {
        yield 'string' => [
            Type::string(),
            'string',
            'getCreationMetadata',
            true
        ];
        yield 'nullable string' => [
            Type::string(),
            '?string',
            'getCreationMetadata',
            true
        ];
        yield 'required string' => [
            Type::nonNull(Type::string()),
            'string',
            'getCreationMetadata',
            false
        ];
        yield 'required nullable string' => [
            Type::string(),
            '?string',
            'getCreationMetadata',
            false
        ];
        // not sure we test anything here.....
        yield 'entity' => [
            Type::nonNull(
                new FromMetadataInputType(
                    MetadataFactory::getCreationMetadata(
                        new ReflectionClass(Order::class),
                        new ApieContext()
                    )
                )
            ),
            Order::class,
            'getCreationMetadata',
            false,
        ];
        yield 'boolean' => [
            Type::boolean(),
            'bool',
            'getCreationMetadata',
            true
        ];
        yield 'floating point' => [
            Type::float(),
            'float',
            'getCreationMetadata',
            true
        ];
        yield 'generic list' => [
            Type::listOf(Type::nonNull(Types::json())),
            ItemList::class,
            'getCreationMetadata',
            true
        ];
        yield 'generic set' => [
            Type::listOf(Type::nonNull(Types::json())),
            ItemSet::class,
            'getCreationMetadata',
            true
        ];
        yield 'string value object' => [
            Type::string(),
            IsStringValueObjectExample::class,
            'getCreationMetadata',
            true
        ];
        yield 'required string value object' => [
            Type::nonNull(Type::string()),
            IsStringValueObjectExample::class,
            'getCreationMetadata',
            false
        ];
        yield 'union type' => [
            Type::string(),
            IsStringValueObjectExample::class . '|' . DatabaseText::class,
            'getCreationMetadata',
            true
        ];
        yield 'required union type' => [
            Type::nonNull(Type::string()),
            IsStringValueObjectExample::class . '|' . DatabaseText::class,
            'getCreationMetadata',
            false
        ];
        yield 'nullable union type' => [
            Type::string(),
            IsStringValueObjectExample::class . '|' . DatabaseText::class . '|null',
            'getCreationMetadata',
            true
        ];
        yield 'required nullable union type' => [
            Type::string(),
            IsStringValueObjectExample::class . '|' . DatabaseText::class . '|null',
            'getCreationMetadata',
            false
        ];
        /*yield 'generic map' => [
            Type::mapOf(Types::json()),
            ItemHashmap::class,
            'getCreationMetadata',
            true
        ];*/
    }
}
