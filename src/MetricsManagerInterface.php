<?php

namespace AvtoDev\AppMetrics;

use InvalidArgumentException;
use AvtoDev\AppMetrics\Metrics\MetricInterface;

interface MetricsManagerInterface
{
    /**
     * Create metric instance by class name or alias.
     *
     * @param string $metric_abstract
     *
     * @throws InvalidArgumentException If passed wrong class name or alias
     *
     * @return MetricInterface
     */
    public function make(string $metric_abstract): MetricInterface;

    /**
     * Determine if metric class is registered.
     *
     * @param string $metric_class
     *
     * @return bool
     */
    public function exists(string $metric_class): bool;

    /**
     * Determine if metric alias is registered.
     *
     * @param string $metric_alias
     *
     * @return bool
     */
    public function aliasExists(string $metric_alias): bool;

    /**
     * Get all metric classes.
     *
     * @return string[]
     */
    public function classes(): array;

    /**
     * Get all metric aliases.
     *
     * @return string[]
     */
    public function aliases(): array;

    /**
     * Create and iterate all available metrics.
     *
     * @return iterable
     */
    public function all(): iterable;
}
