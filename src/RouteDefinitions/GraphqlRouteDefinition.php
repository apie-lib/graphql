<?php
namespace Apie\Graphql\RouteDefinitions;

use Apie\Common\Enums\UrlPrefix;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextConstants;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\Graphql\Controllers\GraphqlController;

class GraphqlRouteDefinition implements HasRouteDefinition
{
    public function __construct(private readonly BoundedContextId $boundedContextId)
    {
    }

    public function getOperationId(): string
    {
        return 'graphql_' . $this->boundedContextId->toNative();
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::POST;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('graphql');
    }

    public function getController(): string
    {
        return GraphqlController::class;
    }
    
    public function getRouteAttributes(): array
    {
        return [
            ContextConstants::BOUNDED_CONTEXT_ID => $this->boundedContextId->toNative(),
        ];
    }

    public function getUrlPrefixes(): UrlPrefixList
    {
        return new UrlPrefixList([UrlPrefix::GRAPHQL]);
    }
}
