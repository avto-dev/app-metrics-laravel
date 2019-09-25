<?php

namespace AvtoDev\AppMetrics\Metrics;

interface MetricsGroupInterface
{
    /**
     * Get metrics collection.
     *
     * Note: Each element must implements MetricInterface.
     *
     * @return MetricInterface[]|iterable
     */
    public function metrics(): iterable;
}
