<?php
namespace Apie\Graphql\TypeResolvers;

use Apie\Core\Actions\ActionInterface;
use Apie\Core\Actions\ApieFacadeInterface;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use ReflectionClass;

class ApieCallTypeResolver
{
    /**
     * @param class-string<ActionInterface> $actionClass
     */
    public function __construct(
        private readonly string $actionClass,
        private readonly ?BoundedContextId $boundedContextId = null,
        private readonly ?ReflectionClass $resourceClass = null,
    ){
    }
    public function __invoke(ApieContext $context, array $args): array
    {
        $contextVariables = $this->actionClass::getRouteAttributes($this->resourceClass);
        $contextVariables[ContextConstants::APIE_ACTION] = $this->actionClass;
        $contextVariables[ContextConstants::RAW_CONTENTS] = $args['input'] ?? [];
        if ($this->boundedContextId) {
            $contextVariables[ContextConstants::BOUNDED_CONTEXT_ID] = $this->boundedContextId->toNative();
            $contextVariables[BoundedContextId::class] = $this->boundedContextId;
        }
        $context = $context->withMultipleContext($contextVariables);
        $facade = $context->getContext(ApieFacadeInterface::class);
        $action = new $this->actionClass($facade);
        $response = $action->__invoke($context, $args['input'] ?? []);

        return json_decode(json_encode($response->getResultAsNativeData()), true);
    }
}