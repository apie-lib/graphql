<?php
namespace Apie\Graphql\Factories;

use Apie\Common\ActionDefinitionProvider;
use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use Apie\Common\ActionDefinitions\DownloadFilesActionDefinition;
use Apie\Common\ActionDefinitions\GetResourceActionDefinition;
use Apie\Common\ActionDefinitions\GetResourceListActionDefinition;
use Apie\Common\ActionDefinitions\ModifyResourceActionDefinition;
use Apie\Common\ActionDefinitions\RemoveResourceActionDefinition;
use Apie\Common\ActionDefinitions\ReplaceResourceActionDefinition;
use Apie\Common\ActionDefinitions\RunResourceMethodDefinition;
use Apie\Common\Actions\CreateObjectAction;
use Apie\Common\Actions\ModifyObjectAction;
use Apie\Common\Actions\RemoveObjectAction;
use Apie\Common\Actions\RunItemMethodAction;
use Apie\Core\ApieLib;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Graphql\TypeResolvers\ApieCallTypeResolver;
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
            'mutation' => $this->createMutations($apieContext),
        ]);
    }

    private function createMutations(ApieContext $apieContext): ObjectType
    {
        $fields = [
        ];
        $boundedContext = $apieContext->getContext(BoundedContext::class);
        foreach ($this->actionDefinitionProvider->provideActionDefinitions($boundedContext, $apieContext) as $actionDefinition) {
            if ($actionDefinition instanceof GetResourceListActionDefinition || $actionDefinition instanceof GetResourceActionDefinition || $actionDefinition instanceof DownloadFilesActionDefinition) {
                continue;
            }
            if ($actionDefinition instanceof CreateResourceActionDefinition || $actionDefinition instanceof ReplaceResourceActionDefinition) {
                $typeInput = Types::createMeta($actionDefinition->getResourceName());
                $type = Types::displayMeta($actionDefinition->getResourceName());
                $fields['create' . $actionDefinition->getResourceName()->getShortName()] = [
                    'name' => 'create' . $actionDefinition->getResourceName()->getShortName(),
                    'type' => $type,
                    'args' => [
                        'input' => [
                            'type' => $typeInput,
                        ],
                    ],
                    'description' => $type->description,
                    'resolve' => new ApieCallTypeResolver(
                        CreateObjectAction::class,
                        $actionDefinition->getBoundedContextId(),
                        $actionDefinition->getResourceName(),
                    ),
                ];
            }
            if ($actionDefinition instanceof RemoveResourceActionDefinition) {
                $fields['remove' . $actionDefinition->getResourceName()->getShortName()] = [
                    'name' => 'remove' . $actionDefinition->getResourceName()->getShortName(),
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => [
                            'type' => Types::fromId($actionDefinition->getResourceName()),
                        ],
                    ],
                    'description' => 'Remove a ' . $actionDefinition->getResourceName()->getShortName() . ' by id',
                    'resolve' => new ApieCallTypeResolver(
                        RemoveObjectAction::class,
                        $actionDefinition->getBoundedContextId(),
                        $actionDefinition->getResourceName(),
                        setResourceId: true
                    ),
                ];
            }
            if ($actionDefinition instanceof ModifyResourceActionDefinition) {
                $typeInput = Types::modifyMeta($actionDefinition->getResourceName());
                $type = Types::displayMeta($actionDefinition->getResourceName());
                $fields['modify' . $actionDefinition->getResourceName()->getShortName()] = [
                    'name' => 'modify' . $actionDefinition->getResourceName()->getShortName(),
                    'type' => $type,
                    'args' => [
                        'id' => [
                            'type' => Types::fromId($actionDefinition->getResourceName()),
                        ],
                        'input' => [
                            'type' => $typeInput
                        ]
                    ],
                    'description' => 'Modifies a single ' . $actionDefinition->getResourceName()->getShortName() . ' by id',
                    'resolve' => new ApieCallTypeResolver(
                        ModifyObjectAction::class,
                        $actionDefinition->getBoundedContextId(),
                        $actionDefinition->getResourceName(),
                        setResourceId: true
                    ),
                ];
            }
            if ($actionDefinition instanceof RunResourceMethodDefinition) {
                $method = $actionDefinition->getMethod();
                $typeInput = Types::methodCallMeta($method);
                $type = Types::displayMeta($actionDefinition->getResourceName());

                $args = [
                        'id' => [
                            'type' => Types::fromId($actionDefinition->getResourceName()),
                        ],
                        'input' => [
                            'type' => $typeInput
                        ]
                    ];
                $description = 'Runs ' . $method->name . ' for a ' . $actionDefinition->getResourceName()->getShortName() . ' by id';
                if ($method->isStatic()) {
                    unset($args['id']);
                    $description = 'Runs ' . $method->name . ' for ' . $actionDefinition->getResourceName()->getShortName();
                }

                $fields['run' . $actionDefinition->getResourceName()->getShortName() . ucfirst($method->name)] = [
                    'name' => 'run' . $actionDefinition->getResourceName()->getShortName() . ucfirst($method->name),
                    'type' => $type,
                    'args' => $args,
                    'description' => $description,
                    'resolve' => new ApieCallTypeResolver(
                        RunItemMethodAction::class,
                        $actionDefinition->getBoundedContextId(),
                        $actionDefinition->getResourceName(),
                        $actionDefinition->getMethod(),
                        !$method->isStatic()
                    ),
                ];
            }
        }
        return new ObjectType([
            'name' => 'Mutation',
            'fields' => $fields,
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
                        'id' => [
                            'type' => Types::fromId($actionDefinition->getResourceName()),
                        ],
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
