<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Metrics;

use Illuminate\Foundation\Application;

class IlluminateFrameworkMetric implements MetricInterface, HasLabelsInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'illuminate';
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function labels(): array
    {
        return [
            'framework_version' => Application::VERSION,
        ];
    }
}
