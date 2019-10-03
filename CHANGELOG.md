# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

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
