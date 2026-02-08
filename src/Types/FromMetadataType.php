<?php
namespace Apie\Graphql\Types;

use Apie\Core\Attributes\Description;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Graphql\Concerns\CreatesFromMeta;
use GraphQL\Type\Definition\ObjectType;
use ReflectionAttribute;

class FromMetadataType extends ObjectType
{
    use CreatesFromMeta;

    public function __construct(MetadataInterface $metadata, string $suffix = '')
    {
        $config = [
            'name' => ($metadata->toClass()?->getShortName() ?? $metadata->toScalarType()->value) . $suffix,
            'fields' => [
            ],
        ];

        foreach ($metadata->toClass()?->getAttributes(Description::class, ReflectionAttribute::IS_INSTANCEOF) ?? [] as $descriptionAttribute) {
            $description = $descriptionAttribute->newInstance();
            $config['description'] ??= '';
            $config['description'] .= $description->description;
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
