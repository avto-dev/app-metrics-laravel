<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

class PrometheusFormatter implements MetricFormatterInterface
{
    /**
     * {@inheritDoc}
     */
    public static function alias(): string
    {
        return 'prometheus';
    }
}
