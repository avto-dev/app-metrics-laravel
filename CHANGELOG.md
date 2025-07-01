# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

## Unreleased

### Added

- Laravel `12.x` support
- Using `docker` with `compose` plugin instead of `docker-compose` for test environment

## v2.8.0

### Added

- Laravel `11.x` support

### Changed

- Minimal Laravel version now is `10.0`
- Minimal require PHP version now is `8.1`
- Version of `composer` in docker container updated up to `2.7.6`
- Updated dev dependencies

## v2.7.0

### Added

- Support Laravel `10.x`

### Changed

- Minimal Laravel version now is `9.1`
- Minimal require PHP version now is `8.0`
- Interface `MetricFormatterInterface` signature

## v2.6.0

### Added

- Support Laravel `9.x`

## v2.5.0

### Added

- Support PHP `8.x`

### Changed

- Minimal PHP version now is `7.3`
- Composer `2.x` is supported now

## v2.4.0

### Changed

- Laravel `8.x` is supported now
- Minimal Laravel version now is `6.0` (Laravel `5.5` LTS got last security update August 30th, 2020)

## v2.3.0

### Changed

- Maximal `illuminate/*` packages version now is `7.*`
- CI completely moved from "Travis CI" to "Github Actions" _(travis builds disabled)_
- Minimal required PHP version now is `7.2`

### Added

- PHP 7.4 is supported now

## v2.2.0

### Added

- Interface `ShouldBeSkippedmetricExceptionInterface`
- Trait `WithThrowableReportingTraitTest.php`
- Possibility to skip metrics if during construction or formatting was thrown exception that implements `ShouldBeSkippedmetricExceptionInterface` (in this case exception would be reported (not thrown out) by `Illuminate\Contracts\Debug\ExceptionHandler` and metric would be skipped from formatters output)

### Changed

- `MetricsManager` now skips metric which throws exception with interface`ShouldBeSkippedmetricExceptionInterface`
during constructing
- `PrometheusFormatter` and `JsonFormatters` now skips metric which throws exception with interface`ShouldBeSkippedmetricExceptionInterface` during formatting

## v2.1.0

### Changed

- Maximal `illuminate/*` packages version now is `6.*`

### Added

- GitHub actions for a tests running

## v2.0.2

### Fixed

- Incorrect prometheus metric types case

## v2.0.1

### Fixed

- Json formatter output example in `README.md`

## v2.0.0

### Added

- Interface `MetricsCollectionInterface`
- Formatters `JsonFormatter` and `PrometheusFormatter` now supports `MetricsCollectionInterface`
- Metric groups can be defined in `metrics.metric_classes`
- `MetricsManagerInterface::iterate(array $abstracts)` method

### Changed

- `MetricsManager` can works with metric groups now
- `MetricsManager::addFactory` allows to register fabric for object that implements `MetricsGroupInterface`
- `MetricsManagerInterface::make` now return `MetricInterface` or `MetricsGroupInterface` (method signature changed)

## v1.0.1

### Fixed

- Fixed missing lead hash symbols for strings that begins with `HELP` and `TYPE` tokens in prometheus formatter [#4]

[#4]: https://github.com/avto-dev/app-metrics-laravel/issues/4

## v1.0.0

### Added

- Managers `FormattersManager`, `MetricsManager`. Possible to load via `DI`
- Controller `MetricsController`
- Middleware `CheckMetricsSecretMiddleware`
- Interfaces for metrics and simple static metrics `HostInfoMetric`, `IlluminateFrameworkMetric`
- Formatters `JsonFormatter`, `PrometheusFormatter`
- Dictionary `PrometheusValuesDictionary.php`

[keepachangelog]:https://keepachangelog.com/en/1.0.0/
[semver]:https://semver.org/spec/v2.0.0.html
