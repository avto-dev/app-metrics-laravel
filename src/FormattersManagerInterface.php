<?php

namespace AvtoDev\AppMetrics;

use LogicException;
use InvalidArgumentException;
use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;

interface FormattersManagerInterface
{
    /**
     * Create formatter instance by formatter alias.
     *
     * @param string $alias
     *
     * @throws InvalidArgumentException If unknown formatter requested
     *
     * @return MetricFormatterInterface
     */
    public function make(string $alias): MetricFormatterInterface;

    /**
     * Determine if formatter is registered.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function exists(string $alias): bool;

    /**
     * Get all formatter aliases.
     *
     * @return string[]
     */
    public function aliases(): array;

    /**
     * Create default formatter.
     *
     * @throws LogicException If default formatter was not set
     *
     * @return MetricFormatterInterface
     */
    public function default(): MetricFormatterInterface;
}
