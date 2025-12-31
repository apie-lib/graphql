<?php
namespace Apie\Graphql\RouteDefinitions;

use Apie\Common\Enums\UrlPrefix;
use Apie\Common\Interfaces\HasRouteDefinition;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextConstants;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\Graphql\Controllers\GraphqlPlaygroundController;

class GraphqlPlaygroundRouteDefinition implements HasRouteDefinition
{
    public function __construct(private readonly BoundedContextId $boundedContextId)
    {
    }

    public function getOperationId(): string
    {
        return 'graphql_playground_' . $this->boundedContextId->toNative();
    }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('/');
    }

    public function getController(): string
    {
        return GraphqlPlaygroundController::class;
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
