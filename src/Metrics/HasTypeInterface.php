<?php

namespace AvtoDev\AppMetrics\Metrics;

interface HasTypeInterface extends MetricInterface
{
    /**
     * Get metric type.
     *
     * Note: Type should be one of supported values.
     *
     * @return string
     */
    public function type(): string;
}
