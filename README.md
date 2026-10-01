<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>graphql</h1>






 [![Latest Stable Version](https://poser.pugx.org/apie/graphql/v)](https://packagist.org/packages/apie/graphql) [![Total Downloads](https://poser.pugx.org/apie/graphql/downloads)](https://packagist.org/packages/apie/graphql) [![Latest Unstable Version](https://poser.pugx.org/apie/graphql/v/unstable)](https://packagist.org/packages/apie/graphql) [![License](https://poser.pugx.org/apie/graphql/license)](https://packagist.org/packages/apie/graphql) [![PHP Composer](https://apie-lib.github.io/projectCoverage/coverage-graphql.svg)](https://apie-lib.github.io/projectCoverage/graphql/index.html)  

[![PHP Composer](https://github.com/apie-lib/graphql/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/graphql/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
GraphQL support for exposing Apie resources and metadata through `webonyx/graphql-php`.

### Standalone usage
Install it with:
```bash
composer require apie/graphql
```

Build a schema with `Apie\Graphql\Factories\GraphqlSchemaFactory` from an `Apie\Common\ActionDefinitionProvider`, and handle requests with `Apie\Graphql\Controllers\GraphqlController`. `GraphqlPlaygroundController` serves an interactive playground, and `DownloadFileController` streams file responses returned by resolvers. The package depends on an Apie persistence layer and domain objects; it does not create those objects for you.

### Symfony integration
Via `apie/apie-bundle`, `graphql.yaml` is loaded automatically and registers the GraphQL route definition provider, schema factory, and controllers as tagged Symfony controllers. The `apie.graphql.base_url` configuration key sets the base URL used by the playground.

### Laravel integration
Via `apie/laravel-apie`, the generated `Apie\Graphql\GraphqlServiceProvider` is auto-registered and wires the same schema factory and controllers into the Laravel container.
