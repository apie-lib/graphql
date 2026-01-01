<?php

declare(strict_types=1);

namespace Apie\Graphql\Controllers;

use Apie\Common\Events\ResponseDispatcher;
use Apie\Common\IntegrationTestLogger;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\Graphql\Factories\GraphqlSchemaFactory;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\FormattedError;
use GraphQL\GraphQL;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class GraphqlController
{
    public function __construct(
        private readonly GraphqlSchemaFactory $schemaFactory,
        private readonly ContextBuilderFactory $contextBuilderFactory,
        private readonly ResponseDispatcher $responseDispatcher
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request, [ContextConstants::GRAPHQL => true]);

        try {
            $payload = $this->parseRequest($request);

            $result = GraphQL::executeQuery(
                schema: $this->schemaFactory->createSchema($context),
                source: $payload['query'] ?? '',
                rootValue: $context,
                variableValues: $payload['variables'] ?? null,
                operationName: $payload['operationName'] ?? null
            );

            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE|DebugFlag::INCLUDE_TRACE);
        } catch (\Throwable $e) {
            IntegrationTestLogger::logException($e);
            $output = [
                'errors' => [
                    FormattedError::createFromException($e),
                ],
            ];
        }

        $psr17Factory = new Psr17Factory();
        $responseBody = $psr17Factory->createStream(json_encode($output, JSON_THROW_ON_ERROR));

        $response = $psr17Factory->createResponse(200)
            ->withBody($responseBody)
            ->withHeader('Content-Type', 'application/json');
        $response = $this->responseDispatcher->triggerResponseCreated($response, $context);

        return $response;
    }

    private function parseRequest(ServerRequestInterface $request): array
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $raw = (string) $request->getBody();
            return json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        }

        return $request->getParsedBody() ?? [];
    }
}
