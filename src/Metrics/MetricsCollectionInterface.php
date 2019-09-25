<?php

namespace AvtoDev\AppMetrics\Metrics;

interface MetricsCollectionInterface
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
