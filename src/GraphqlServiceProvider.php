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
        $this->app->singleton(
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
        $this->app->singleton(
            \Apie\Graphql\Factories\GraphqlSchemaFactory::class,
            function ($app) {
                return new \Apie\Graphql\Factories\GraphqlSchemaFactory(
                    $app->make(\Apie\Common\ActionDefinitionProvider::class)
                );
            }
        );
        $this->app->singleton(
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
        $this->app->singleton(
            \Apie\Graphql\Controllers\GraphqlPlaygroundController::class,
            function ($app) {
                return new \Apie\Graphql\Controllers\GraphqlPlaygroundController(
                    $this->parseArgument('%apie.graphgl_baseurl', \Apie\Graphql\Controllers\GraphqlPlaygroundController::class, 0),
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
