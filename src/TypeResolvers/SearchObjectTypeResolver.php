<?php
namespace Apie\Graphql\TypeResolvers;

use Apie\Core\Context\ApieContext;
use Apie\Serializer\Lists\SerializedHashmap;

class SearchObjectTypeResolver
{
    public function __invoke(ApieContext $context, array $args): SerializedHashmap
    {
        // Dummy implementation for illustration purposes
        return new SerializedHashmap([
            'results' => ['result1', 'result2', 'result3'],
            'totalCount' => 3,
        ]);
    }
}
