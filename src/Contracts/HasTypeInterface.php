<?php

namespace AvtoDev\AppMetrics\Contracts;

interface HasTypeInterface
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
