<?php

namespace AvtoDev\AppMetrics\Metrics;

interface HasDescriptionInterface
{
    /**
     * Get metric description.
     *
     * @return string
     */
    public function description(): string;
}
