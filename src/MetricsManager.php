<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Illuminate\Contracts\Container\Container;

class MetricsManager implements MetricsManagerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string[]
     */
    protected $metrics;

    /**
     * Create a new MetricsManager instance.
     *
     * @param Container $container
     * @param string[]  $metrics Metric class names, e.g.: `[FooMetric::class, 'bar' => BarMetric::class]`
     */
    public function __construct(Container $container, array $metrics)
    {
        $this->container = $container;
        $this->metrics   = $metrics;
    }
}
