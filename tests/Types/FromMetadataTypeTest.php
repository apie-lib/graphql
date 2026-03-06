<?php
namespace Apie\Tests\Graphql\Factories;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\ItemSet;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Core\ValueObjects\FileUri;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\ValueObjects\IsStringValueObjectExample;
use Apie\Graphql\Types;
use Apie\Graphql\Types\FromMetadataType;
use Apie\Graphql\Types\MapOfType;
use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use GraphQL\Type\Definition\StringType;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FromMetadataTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('metadataProvider')]
    public function it_can_create_a_type_definition(
        Type $expected,
        string $typehint,
        string $metadataMethod,
        bool $nullable
    ) {
        Types::clear();
        $metadata = MetadataFactory::$metadataMethod(
            ReflectionTypeFactory::createReflectionType($typehint),
            new ApieContext([])
        );
        $actual = FromMetadataType::createFromMetadata($metadata, $nullable);
        $this->assertEquals($expected, $actual);
    }

    public static function metadataProvider(): Generator
    {
        yield 'string' => [
            Type::string(),
            'string',
            'getResultMetadata',
            true
        ];
        yield 'nullable string' => [
            Type::string(),
            '?string',
            'getResultMetadata',
            true
        ];
        yield 'required string' => [
            Type::nonNull(Type::string()),
            'string',
            'getResultMetadata',
            false
        ];
        yield 'required nullable string' => [
            Type::string(),
            '?string',
            'getResultMetadata',
            false
        ];
        /*yield 'entity' => [
            Type::nonNull(
                new FromMetadataType(
                    MetadataFactory::getResultMetadata(
                        new ReflectionClass(Order::class),
                        new ApieContext()
                    )
                )
            ),
            Order::class,
            'getResultMetadata',
            false,
        ];*/
        yield 'boolean' => [
            Type::boolean(),
            'bool',
            'getResultMetadata',
            true
        ];
        yield 'floating point' => [
            Type::float(),
            'float',
            'getResultMetadata',
            true
        ];
        yield 'generic list' => [
            Type::listOf(Type::nonNull(Types::json())),
            ItemList::class,
            'getResultMetadata',
            true
        ];
        yield 'generic set' => [
            Type::listOf(Type::nonNull(Types::json())),
            ItemSet::class,
            'getResultMetadata',
            true
        ];
        yield 'string value object' => [
            Type::string(),
            IsStringValueObjectExample::class,
            'getResultMetadata',
            true
        ];
        yield 'required string value object' => [
            Type::nonNull(Type::string()),
            IsStringValueObjectExample::class,
            'getResultMetadata',
            false
        ];
        yield 'union type' => [
            Type::string(),
            IsStringValueObjectExample::class . '|' . DatabaseText::class,
            'getResultMetadata',
            true
        ];
        yield 'required union type' => [
            Type::nonNull(Type::string()),
            IsStringValueObjectExample::class . '|' . DatabaseText::class,
            'getResultMetadata',
            false
        ];
        yield 'nullable union type' => [
            Type::string(),
            IsStringValueObjectExample::class . '|' . DatabaseText::class . '|null',
            'getResultMetadata',
            true
        ];
        yield 'required nullable union type' => [
            Type::string(),
            IsStringValueObjectExample::class . '|' . DatabaseText::class . '|null',
            'getResultMetadata',
            false
        ];
        yield 'generic map' => [
            new MapOfType(Type::nonNull(Types::json())),
            ItemHashmap::class,
            'getResultMetadata',
            true
        ];
        yield 'required generic map' => [
            Type::nonNull(new MapOfType(Type::nonNull(Types::json()))),
            ItemHashmap::class,
            'getResultMetadata',
            false
        ];
        yield 'file uri' => [
            Type::nonNull(
                new StringType([
                    'name' => 'FileUri',
                    'description' => 'URL to download the file',
                ])
            ),
            FileUri::class,
            'getResultMetadata',
            false
        ];
        yield 'file uri nullable' => [
            new StringType([
                'name' => 'FileUri',
                'description' => 'URL to download the file',
            ]),
            FileUri::class,
            'getResultMetadata',
            true
        ];
    }
}
