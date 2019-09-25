# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

## v1.1.0

### Added

- Interface `MetricsCollectionInterface`
- Formatters `JsonFormatter` and `PrometheusFormatter` now supports `MetricsCollectionInterface`

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
