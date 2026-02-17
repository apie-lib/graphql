<?php
namespace Apie\Graphql;

use Apie\ServiceProviderGenerator\UseGeneratedMethods;
use Illuminate\Support\ServiceProvider;

/**
 * This file is generated with apie/service-provider-generator from file: graphql.yaml
 * @codeCoverageIgnore
 */
class GraphqlServiceProvider extends ServiceProvider
{
    use UseGeneratedMethods;

    public function register()
    {
        $this->registerSingleton(
            \Apie\Graphql\RouteDefinitions\GraphqlRouteDefinitionProvider::class,
            function ($app) {
                return new \Apie\Graphql\RouteDefinitions\GraphqlRouteDefinitionProvider(
                    $app->make(\Apie\Common\ActionDefinitionProvider::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Graphql\RouteDefinitions\GraphqlRouteDefinitionProvider::class,
            array(
              0 =>
              array(
                'name' => 'apie.common.route_definition',
              ),
            )
        );
        $this->app->tag([\Apie\Graphql\RouteDefinitions\GraphqlRouteDefinitionProvider::class], 'apie.common.route_definition');
        $this->registerSingleton(
            \Apie\Graphql\Factories\GraphqlSchemaFactory::class,
            function ($app) {
                return new \Apie\Graphql\Factories\GraphqlSchemaFactory(
                    $app->make(\Apie\Common\ActionDefinitionProvider::class)
                );
            }
        );
        $this->registerSingleton(
            \Apie\Graphql\Controllers\DownloadFileController::class,
            function ($app) {
                return new \Apie\Graphql\Controllers\DownloadFileController(
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make('apie'),
                    $app->make(\Apie\Serializer\EncoderHashmap::class),
                    $app->make(\Apie\Common\Events\ResponseDispatcher::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Graphql\Controllers\DownloadFileController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Graphql\Controllers\DownloadFileController::class], 'controller.service_arguments');
        $this->registerSingleton(
            \Apie\Graphql\Controllers\GraphqlController::class,
            function ($app) {
                return new \Apie\Graphql\Controllers\GraphqlController(
                    $app->make(\Apie\Graphql\Factories\GraphqlSchemaFactory::class),
                    $app->make(\Apie\Core\ContextBuilders\ContextBuilderFactory::class),
                    $app->make(\Apie\Common\Events\ResponseDispatcher::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Graphql\Controllers\GraphqlController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Graphql\Controllers\GraphqlController::class], 'controller.service_arguments');
        $this->registerSingleton(
            \Apie\Graphql\Controllers\GraphqlPlaygroundController::class,
            function ($app) {
                return new \Apie\Graphql\Controllers\GraphqlPlaygroundController(
                    $this->parseArgument('%apie.graphql.base_url%', \Apie\Graphql\Controllers\GraphqlPlaygroundController::class, 0),
                    $app->make(\Apie\Core\BoundedContext\BoundedContextHashmap::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Graphql\Controllers\GraphqlPlaygroundController::class,
            array(
              0 => 'controller.service_arguments',
            )
        );
        $this->app->tag([\Apie\Graphql\Controllers\GraphqlPlaygroundController::class], 'controller.service_arguments');
        
    }
}
