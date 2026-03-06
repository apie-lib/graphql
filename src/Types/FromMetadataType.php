<?php
namespace Apie\Graphql\Types;

use Apie\Core\Attributes\Description;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\Graphql\Concerns\CreatesFromMeta;
use GraphQL\Type\Definition\ObjectType;
use ReflectionAttribute;

class FromMetadataType extends ObjectType
{
    use CreatesFromMeta;

    public function __construct(MetadataInterface $metadata, string $suffix = '')
    {
        $class = $metadata->toClass();
        $name = $class ? Utils::getDisplayNameForValueObject($class) : $metadata->toScalarType()->value;
        $config = [
            'name' => $name . $suffix,
            'fields' => [
            ],
        ];

        foreach ($metadata->toClass()?->getAttributes(Description::class, ReflectionAttribute::IS_INSTANCEOF) ?? [] as $descriptionAttribute) {
            $description = $descriptionAttribute->newInstance();
            $config['description'] = $description->description;
        }

        foreach ($metadata->getHashmap() as $name => $field) {
            if ($field->isField()) {
                $config['fields'][$name] = [
                    'type' => self::createFromField($field),
                ];
            }
        }
        parent::__construct($config);
    }
}
