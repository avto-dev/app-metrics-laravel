<?php

namespace AvtoDev\AppMetrics\Contracts;

interface HasDescriptionInterface
{
    /**
     * Get metric description.
     *
     * @return string
     */
    public function description(): string;
}
