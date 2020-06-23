<?php

namespace AvtoDev\AppMetrics\Metrics;

interface MetricInterface
{
    /**
     * Get metric name (key).
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get metric value (or array of values).
     *
     * @return string|int|float|array<mixed>
     */
    public function value();
}
