<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Metrics;

use Illuminate\Foundation\Application;

class IlluminateFrameworkMetric implements MetricInterface, HasLabelsInterface
{
    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'illuminate';
    }

    /**
     * {@inheritDoc}
     */
    public function value()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
     */
    public function labels(): array
    {
        return [
            'framework_version' => Application::VERSION,
        ];
    }
}
