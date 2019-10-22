<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricException;

class SkippingMetric implements MetricInterface
{
    /**
     * Creates stub-metric that should be skipped.
     *
     * @throws ShouldBeSkippedMetricException
     */
    public function __construct()
    {
        throw new ShouldBeSkippedMetricException('Metric should be skipped');
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
