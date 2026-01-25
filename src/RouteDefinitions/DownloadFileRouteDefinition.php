<?php
namespace Apie\Graphql\RouteDefinitions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\DownloadFilesActionDefinition;
use Apie\Common\Actions\StreamItemMethodAction;
use Apie\Common\Enums\UrlPrefix;
use Apie\Common\Lists\UrlPrefixList;
use Apie\Common\RouteDefinitions\AbstractRestApiRouteDefinition;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\Graphql\Controllers\DownloadFileController;
use ReflectionClass;

class DownloadFileRouteDefinition extends AbstractRestApiRouteDefinition
{
    /**
     * @param ReflectionClass<EntityInterface> $className
     */
    public function __construct(ReflectionClass $className, BoundedContextId $boundedContextId)
    {
        parent::__construct($className, $boundedContextId);
    }

    public function getController(): string
    {
        return DownloadFileController::class;
    }

    public function getUrlPrefixes(): UrlPrefixList
    {
        return new UrlPrefixList([UrlPrefix::GRAPHQL]);
    }

    public function getOperationId(): string
    {
        return 'stream-graphql-' . $this->class->getShortName() . '-run-download';
    }
    
    public function getMethod(): RequestMethod
    {
        return RequestMethod::GET;
    }

    public function getUrl(): UrlRouteDefinition
    {
        return new UrlRouteDefinition('/' . $this->class->getShortName() . '/{id}/download/{properties}');
    }

    public function getAction(): string
    {
        return StreamItemMethodAction::class;
    }

    public static function createFrom(ActionDefinitionInterface $actionDefinition): ?AbstractRestApiRouteDefinition
    {
        if ($actionDefinition instanceof DownloadFilesActionDefinition) {
            return new self($actionDefinition->getResourceName(), $actionDefinition->getBoundedContextId());
        }
        return null;
    }
}
