<?php
namespace Apie\Graphql\TypeResolvers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Serializer\Serializer;

class SearchObjectTypeResolver
{
    public function __construct(private readonly string $resourceName)
    {
    }
    public function __invoke(ApieContext $context, array $args): array
    {
        $apieDatalayer = $context->getContext(ApieDatalayer::class);
        assert($apieDatalayer instanceof ApieDatalayer);
        $serializer = $context->getContext(Serializer::class);
        assert($serializer instanceof Serializer);
        $resourceName = new \ReflectionClass($this->resourceName);
        $boundedContextId = new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID));

        $list = $apieDatalayer->all($resourceName, $boundedContextId)
            ->toPaginatedResult(QuerySearch::fromCamelCaseArray(
                $args['filter'] ?? [],
                $context
            ));

        return $serializer->normalize($list, $context)->toArray();
    }
}
