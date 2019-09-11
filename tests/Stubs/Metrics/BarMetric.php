<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricInterface;

class BarMetric implements MetricInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'bar';
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return 'bar value';
    }
}
