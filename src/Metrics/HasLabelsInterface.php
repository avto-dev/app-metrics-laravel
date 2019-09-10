<?php

namespace AvtoDev\AppMetrics\Metrics;

interface HasLabelsInterface
{
    /**
     * Get metric labels.
     *
     * @return string[]|int[]|float[]
     */
    public function labels(): array;
}
