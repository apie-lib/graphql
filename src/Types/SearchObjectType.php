<?php
namespace Apie\Graphql\Types;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Graphql\TypeResolvers\SearchObjectTypeResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use ReflectionClass;

class SearchObjectType extends ObjectType
{
    public function __construct(ReflectionClass $resourceClass)
    {
        $metadata = MetadataFactory::getCreationMetadata($resourceClass, new ApieContext());
        $config = [
            'name' => 'find' . $resourceClass->getShortName(),
            'fields' => [
                'results' => [
                    'type' => Type::listOf(FromMetadataType::createFromMetadata($metadata)),
                    'description' => 'The search results',
                ],
                'totalCount' => [
                    'type' => Type::int(),
                    'description' => 'Total number of results found',
                ],
            ],
            'description' => 'Return search results for ' . $resourceClass->getShortName(),
            'resolve' => new SearchObjectTypeResolver(),
        ];
        parent::__construct($config);
    }
}
