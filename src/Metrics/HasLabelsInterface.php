<?php

namespace AvtoDev\AppMetrics\Metrics;

interface HasLabelsInterface extends MetricInterface
{
    /**
     * Get metric labels.
     *
     * @return string[]|int[]|float[]
     */
    public function labels(): array;
}
