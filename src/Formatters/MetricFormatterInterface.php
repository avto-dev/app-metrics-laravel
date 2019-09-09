<?php

namespace AvtoDev\AppMetrics\Formatters;

interface MetricFormatterInterface
{
    /**
     * Get formatter alias.
     *
     * @return string
     */
    public static function alias(): string;
}
