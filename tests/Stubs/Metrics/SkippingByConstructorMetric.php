<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Tests\Stubs\Exceptions\ShouldBeSkippedException;

class SkippingByConstructorMetric implements MetricInterface
{
    /**
     * Creates stub-metric that should be skipped.
     *
     * @throws ShouldBeSkippedException
     */
    public function __construct()
    {
        throw new ShouldBeSkippedException('Metric should be skipped');
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'foo';
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return 'foo value';
    }
}
