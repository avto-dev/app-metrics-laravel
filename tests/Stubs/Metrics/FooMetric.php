<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricInterface;

class FooMetric implements MetricInterface
{
    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'foo';
    }

    /**
     * {@inheritDoc}
     */
    public function value()
    {
        return 'foo value';
    }
}
