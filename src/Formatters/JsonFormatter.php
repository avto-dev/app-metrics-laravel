<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

class JsonFormatter implements MetricFormatterInterface
{
    /**
     * {@inheritDoc}
     */
    public static function alias(): string
    {
        return 'json';
    }
}
