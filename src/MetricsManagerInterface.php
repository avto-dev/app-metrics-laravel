<?php

namespace AvtoDev\AppMetrics;

use InvalidArgumentException;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;

interface MetricsManagerInterface
{
    /**
     * Create metric instance or metrics group by class name or alias.
     *
     * @param string $metric_abstract
     *
     * @throws InvalidArgumentException If passed wrong class name or alias
     *
     * @return MetricInterface|MetricsGroupInterface
     */
    public function make(string $metric_abstract);

    /**
     * Determine if metric or group class is registered.
     *
     * @param string $metric_class
     *
     * @return bool
     */
    public function exists(string $metric_class): bool;

    /**
     * Determine if metric or group alias is registered.
     *
     * @param string $metric_alias
     *
     * @return bool
     */
    public function aliasExists(string $metric_alias): bool;

    /**
     * Get all metric and group classes.
     *
     * @return string[]
     */
    public function classes(): array;

    /**
     * Get all metric and group aliases.
     *
     * @return string[]
     */
    public function aliases(): array;

    /**
     * Create and iterate metrics with passed classes or aliases.
     *
     * Note: Metrics, declared in groups will be returned in a one "flat" set.
     *
     * @param string[] $abstracts
     *
     * @return MetricInterface[]|iterable
     */
    public function iterate(array $abstracts): iterable;

    /**
     * Create and iterate all available metrics.
     *
     * Note: Metrics, declared in groups will be returned in a one "flat" set.
     *
     * @return MetricInterface[]|iterable
     */
    public function iterateAll(): iterable;
}
