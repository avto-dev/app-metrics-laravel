<p align="center">
  <img src="https://laravel.com/assets/img/components/logo-laravel.svg" alt="Laravel" width="240" />
</p>

# Metrics endpoint for Laravel applications

[![Version][badge_packagist_version]][link_packagist]
[![PHP Version][badge_php_version]][link_packagist]
[![Build Status][badge_build_status]][link_build_status]
[![Coverage][badge_coverage]][link_coverage]
[![Downloads count][badge_downloads_count]][link_packagist]
[![License][badge_license]][link_license]

Using this package provides easy way for integration metrics endpoint into your Illuminate-based application.

## Install

Require this package with composer using the following command:

```bash
$ composer require avto-dev/app-metrics-laravel "^2.0"
```

> Installed `composer` is required ([how to install composer][getcomposer]).

> You need to fix the major version of package.

After that you should "publish" package configuration file using next command:

```bash
$ php ./artisan vendor:publish --provider="AvtoDev\\AppMetrics\\ServiceProvider"
```

And configure it in the file `./config/metrics.php`.

## Usage

Feel free to write your own metric classes and add it after into `metrics.metric_classes` configuration array.

> Metric class must implements `MetricInterface` and optionally `HasDescriptionInterface`/`HasLabelsInterface`/`HasTypeInterface`. In metric constructor you can request any dependencies - DI will inject it automatically.

> Also metric class can implements `MetricsGroupInterface` (for metrics grouping).

When your metric classes are ready and successfully registered into configuration file - you can request special route (`/metrics` by default):

```bash
$ curl http://127.0.0.1:8080/metrics?format=json
[{"name": "some_metric", "value": 1, "labels": {"foo":"bar"}}]

$ curl http://127.0.0.1:8080/metrics?format=prometheus
some_metric{foo="bar"} 1
```

> Configuration allows you to change endpoint URI, set password protection, and more.

Secret verification can be completed using sending get-parameter `?secret=XXX` or HTTP header `X-SECRET`.

### Formatters

Out of the box this package provides `json` and `prometheus` metrics formats. Feel free to write your own formatters.

#### Prometheus formatter

Prometheus formatter uses some constants for metric's values(`Nan`, `+Inf`, `-Inf`) and types(`counter`,`summary`,`gauge`,`histogram`, `untyped`). You can find it in [`PrometheusValuesDictionary`](./src/Formatters/Dictionaries/PrometheusValuesDictionary.php) and [`HasTypeInterface`](./src/Metrics/HasTypeInterface.php)

Keep in mind that `Prometheus` accepts only numeric metrics values. This formatter will try to cast metrics values to numeric format (`integer` or `float`). And will set `Nan` if casting to numeric is impossible. Examples for formatting values:

Input          | Output
-------------- | --------
`1.2`          | `'1.2'`
`1`            | `'1'`
`true`         | `'1'`
`false`        | `'0'`
`'123'`        | `'123'`
`'12foo'`      | `'Nan'`
`['10', '20']` | `'Nan'`
`null`         | `'Nan'`
`'Nan'`        | `'Nan'`
`'+Inf'`       | `'+Inf'`
`'-Inf'`       | `'-Inf'`

### Skipping metrics

If during construction or formatting metrics would be thrown exception that implements `AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface` then this metric would be skipped from formatters output and exception would be reported by `Illuminate\Contracts\Debug\ExceptionHandler`

### Testing

For package testing we use `phpunit` framework and `docker-ce` + `docker-compose` as develop environment. So, just write into your terminal after repository cloning:

```bash
$ make build
$ make latest # or 'make lowest'
$ make test
```

## Changes log

[![Release date][badge_release_date]][link_releases]
[![Commits since latest release][badge_commits_since_release]][link_commits]

Changes log can be [found here][link_changes_log].

## Support

[![Issues][badge_issues]][link_issues]
[![Issues][badge_pulls]][link_pulls]

If you will find any package errors, please, [make an issue][link_create_issue] in current repository.

## License

This is open-sourced software licensed under the [MIT License][link_license].

[badge_packagist_version]:https://img.shields.io/packagist/v/avto-dev/app-metrics-laravel.svg?maxAge=180
[badge_php_version]:https://img.shields.io/packagist/php-v/avto-dev/app-metrics-laravel.svg?longCache=true
[badge_build_status]:https://img.shields.io/github/workflow/status/avto-dev/app-metrics-laravel/tests/master
[badge_coverage]:https://img.shields.io/codecov/c/github/avto-dev/app-metrics-laravel/master.svg?maxAge=60
[badge_downloads_count]:https://img.shields.io/packagist/dt/avto-dev/app-metrics-laravel.svg?maxAge=180
[badge_license]:https://img.shields.io/packagist/l/avto-dev/app-metrics-laravel.svg?longCache=true
[badge_release_date]:https://img.shields.io/github/release-date/avto-dev/app-metrics-laravel.svg?style=flat-square&maxAge=180
[badge_commits_since_release]:https://img.shields.io/github/commits-since/avto-dev/app-metrics-laravel/latest.svg?style=flat-square&maxAge=180
[badge_issues]:https://img.shields.io/github/issues/avto-dev/app-metrics-laravel.svg?style=flat-square&maxAge=180
[badge_pulls]:https://img.shields.io/github/issues-pr/avto-dev/app-metrics-laravel.svg?style=flat-square&maxAge=180
[link_releases]:https://github.com/avto-dev/app-metrics-laravel/releases
[link_packagist]:https://packagist.org/packages/avto-dev/app-metrics-laravel
[link_build_status]:https://travis-ci.org/avto-dev/app-metrics-laravel
[link_coverage]:https://codecov.io/gh/avto-dev/app-metrics-laravel/
[link_changes_log]:https://github.com/avto-dev/app-metrics-laravel/blob/master/CHANGELOG.md
[link_issues]:https://github.com/avto-dev/app-metrics-laravel/issues
[link_create_issue]:https://github.com/avto-dev/app-metrics-laravel/issues/new/choose
[link_commits]:https://github.com/avto-dev/app-metrics-laravel/commits
[link_pulls]:https://github.com/avto-dev/app-metrics-laravel/pulls
[link_license]:https://github.com/avto-dev/app-metrics-laravel/blob/master/LICENSE
[getcomposer]:https://getcomposer.org/download/
