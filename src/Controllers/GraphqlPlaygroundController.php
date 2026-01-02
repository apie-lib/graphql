<?php
namespace Apie\Graphql\Controllers;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GraphqlPlaygroundController
{
    private readonly string $htmlPath;

    public function __construct(
        private readonly string $baseUrl,
        private readonly BoundedContextHashmap $boundedContextHashmap,
        ?string $htmlPath = null
    ) {
        $this->htmlPath = null === $htmlPath ? __DIR__ . '/../../resources/playground/index.html' : $htmlPath;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $boundedContextId = $request->getAttribute('boundedContextId');
        $search = [
            '%%GRAPHQL_ENDPOINT%%',
            '%%GRAPHQL_ENDPOINT_OPTIONS%%',
        ];
        $boundedContext = $this->boundedContextHashmap[$boundedContextId];
        $urls = [
            [
                'endpoint' => '/' . $boundedContextId . '/' . ltrim($this->baseUrl, '/'),
                'name' => $boundedContextId,
            ]
        ];
        
        foreach ($this->boundedContextHashmap as $availableBoundedContextId => $boundedContext) {
            if ($boundedContextId !== $availableBoundedContextId) {
                $urls[] = [
                    'endpoint' => '/' . $availableBoundedContextId . '/' . ltrim($this->baseUrl, '/'),
                    'name' => $availableBoundedContextId,
                ];
            }
        }
        $replace = [
            '/' . $boundedContextId . '/' . ltrim($this->baseUrl, '/'),
            json_encode($urls, JSON_UNESCAPED_SLASHES),
        ];

        $responseBody = str_replace($search, $replace, file_get_contents($this->htmlPath));
        $psr17Factory = new Psr17Factory();
        return $psr17Factory->createResponse(200)
            ->withBody($psr17Factory->createStream($responseBody))
            ->withHeader('Content-Type', 'text/html');
    }
}
