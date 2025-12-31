<?php
namespace Apie\Graphql\RouteDefinitions;

use Apie\Common\Interfaces\RouteDefinitionProviderInterface;
use Apie\Common\RouteDefinitions\ActionHashmap;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;

final class GraphqlRouteDefinitionProvider implements RouteDefinitionProviderInterface
{
    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap
    {
        $routes = [];
        $definition = new GraphqlPlaygroundRouteDefinition($boundedContext->getId());
        $routes[$definition->getOperationId()] = $definition;
        $definition = new GraphqlRouteDefinition($boundedContext->getId());
        $routes[$definition->getOperationId()] = $definition;

        return new ActionHashmap($routes);
    }
}
