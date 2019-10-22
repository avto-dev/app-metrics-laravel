<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Tests\Stubs\Exceptions\ShouldBeSkippedException;

class SkippingByValueMethodMetric implements MetricInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'foo';
    }

    /**
     * {@inheritdoc}
     *
     * @throws ShouldBeSkippedException
     */
    public function value()
    {
        throw new ShouldBeSkippedException('Metric should be skipped');
    }
}
