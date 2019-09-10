<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricInterface;

class BarMetric implements MetricInterface
{
    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'bar';
    }

    /**
     * {@inheritDoc}
     */
    public function value()
    {
        return 'bar value';
    }
}
