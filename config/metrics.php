<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Metric Classes
    |--------------------------------------------------------------------------
    |
    | Feel free to add your own metric classes (that implements
    | `MetricInterface`) to this array. Also you can define here metric groups
    | (group is object, that implements `MetricsGroupInterface` interface).
    |
    | For example:
    |
    | ```
    | Metrics\FooMetric::class,
    | 'bar' => Metrics\BarMetric::class, // 'bar' is metric alias
    | 'blah' => Metrics\MetricsGroup::class,
    | ```
    |
    */
    'metric_classes' => [
        AvtoDev\AppMetrics\Metrics\IlluminateFrameworkMetric::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Available Metric Formatters
    |--------------------------------------------------------------------------
    |
    | Formatters allows to represent metrics data in different formats. Array
    | key is formatter alias (short name; required).
    |
    */
    'formatters'     => [
        'json'       => AvtoDev\AppMetrics\Formatters\JsonFormatter::class,
        'prometheus' => AvtoDev\AppMetrics\Formatters\PrometheusFormatter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Metric Format
    |--------------------------------------------------------------------------
    |
    | By default will be used format defined here (formatter for this format
    | must be defined in `formatters` array).
    |
    */
    'default_format' => 'json',

    /*
    |--------------------------------------------------------------------------
    | HTTP Route Settings
    |--------------------------------------------------------------------------
    |
    | Metrics can be accessible through HTTP request (get). Available options:
    |
    | - `enabled` - Set `false` to disable HTTP route;
    | - `uri` - URI path;
    | - `name` - Route "name";
    | - `controller` - Route controller;
    | - `secret` - Secret access key (for `CheckMetricsSecretMiddleware`).
    |
    */
    'http'           => [
        'enabled'    => (bool) env('METRICS_HTTP_ENABLED', true),
        'uri'        => env('METRICS_HTTP_URI', '/metrics'),
        'name'       => 'app.metrics',
        'controller' => AvtoDev\AppMetrics\Http\Controllers\MetricsController::class,
        'secret'     => env('METRICS_HTTP_SECRET'),
    ],

];
