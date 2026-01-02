<?php
namespace Apie\Graphql\Types;

use Apie\Core\Metadata\MetadataInterface;
use Apie\Graphql\Concerns\CreatesFromMeta;
use GraphQL\Type\Definition\InputObjectType;

class FromMetadataInputType extends InputObjectType
{
    use CreatesFromMeta;

    public function __construct(MetadataInterface $metadata, string $suffix = '')
    {
        $config = [
            'name' => ($metadata->toClass()?->getShortName() ?? $metadata->toScalarType()->value) . $suffix,
            'fields' => [
            ],
        ];
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
