<?php
namespace Apie\Graphql\Factories;

use Apie\Common\ActionDefinitionProvider;
use Apie\Common\ActionDefinitions\GetResourceListActionDefinition;
use Apie\Core\ApieLib;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
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
                $fields[$type->name] = $type;
            }
        }
        return new ObjectType([
            'name' => 'Query',
            'fields' => $fields,
        ]);
    }
}
