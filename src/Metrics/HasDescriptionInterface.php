<?php

namespace AvtoDev\AppMetrics\Metrics;

interface HasDescriptionInterface extends MetricInterface
{
    /**
     * Get metric description.
     *
     * @return string
     */
    public function description(): string;
}
