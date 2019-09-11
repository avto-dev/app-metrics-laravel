<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Formatters;

use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;

class BarFormatter implements MetricFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(iterable $metrics)
    {
    }
}
