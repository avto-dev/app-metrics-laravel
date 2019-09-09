<?php

namespace AvtoDev\AppMetrics\Contracts;

interface HasLabelsInterface
{
    /**
     * Get metric labels.
     *
     * @return string[]|int[]|float[]
     */
    public function labels(): array;
}
