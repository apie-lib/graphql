<?php
namespace Apie\Graphql\Types;

use Apie\Graphql\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use ReflectionClass;

class SearchObjectType extends ObjectType
{
    public function __construct(ReflectionClass $resourceClass)
    {
        $config = [
            'name' => 'find' . $resourceClass->getShortName(),
            'fields' => [
                'list' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(Types::displayMeta($resourceClass)))),
                    'description' => 'The search results',
                ],
                'totalCount' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Total number of results found',
                ],
                'filteredCount' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Total number of filtered results found',
                ]
            ],
            'description' => 'Return search results for ' . $resourceClass->getShortName(),
        ];
        parent::__construct($config);
    }
}
