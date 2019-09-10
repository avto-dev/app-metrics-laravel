<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Formatters;

use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;

class BarFormatter implements MetricFormatterInterface
{
    /**
     * {@inheritDoc}
     */
    public function format(iterable $metrics)
    {
        return null;
    }
}
