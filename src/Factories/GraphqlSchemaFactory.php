<?php
namespace Apie\Graphql\Factories;

use Apie\Common\ActionDefinitionProvider;
use Apie\Common\ActionDefinitions\GetResourceListActionDefinition;
use Apie\Core\ApieLib;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Graphql\TypeResolvers\SearchObjectTypeResolver;
use Apie\Graphql\Types;
use Apie\Graphql\Types\SearchObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

class GraphqlSchemaFactory
{
    public function __construct(
        private readonly ActionDefinitionProvider $actionDefinitionProvider,
    ) {
    }
    public function createSchema(ApieContext $apieContext): Schema
    {
        return new Schema([
            'query' => $this->createQuery($apieContext),
        ]);
    }

    private function createQuery(ApieContext $apieContext): ObjectType
    {
        $fields = [
            'apie_version' => [
                'type' => Type::string(),
                'args' => [],
                'description' => 'Get the current version of the Apie library',
                'resolve' => fn (ApieContext $rootValue, array $args): string => ApieLib::VERSION,
            ],
        ];
        $boundedContext = $apieContext->getContext(BoundedContext::class);
        foreach ($this->actionDefinitionProvider->provideActionDefinitions($boundedContext, $apieContext) as $actionDefinition) {
            if ($actionDefinition instanceof GetResourceListActionDefinition) {
                $type = new SearchObjectType($actionDefinition->getResourceName());
                $fields[$type->name] = [
                    'type' => $type,
                    'args' => [
                        'filter' => [
                            'type' => Types::createMeta(new \ReflectionClass(QuerySearch::class)),
                        ],
                    ],
                    'description' => $type->description,
                    'resolve' => new SearchObjectTypeResolver($actionDefinition->getResourceName()->name),
                ];
            }
        }
        return new ObjectType([
            'name' => 'Query',
            'fields' => $fields,
        ]);
    }
}
