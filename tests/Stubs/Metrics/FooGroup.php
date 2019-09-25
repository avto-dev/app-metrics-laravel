<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Metrics;

use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;

class FooGroup implements MetricsGroupInterface
{
    public function metrics(): iterable
    {
        return [
            new FooMetric,
            new BarMetric,
        ];
    }
}
