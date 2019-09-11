<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Metrics;

use Illuminate\Foundation\Application;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Metrics\IlluminateFrameworkMetric;

/**
 * @covers \AvtoDev\AppMetrics\Metrics\IlluminateFrameworkMetric<extended>
 */
class IlluminateFrameworkMetricTest extends AbstractUnitTestCase
{
    /**
     * @var IlluminateFrameworkMetric
     */
    protected $metric;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->metric = $this->app->make(IlluminateFrameworkMetric::class);
    }

    /**
     * @return void
     */
    public function testImplements(): void
    {
        $this->assertInstanceOf(MetricInterface::class, $this->metric);
        $this->assertInstanceOf(HasLabelsInterface::class, $this->metric);
    }

    /**
     * @return void
     */
    public function testGetters(): void
    {
        $this->assertSame('illuminate', $this->metric->name());
        $this->assertSame(1, $this->metric->value());
        $this->assertSame(Application::VERSION, $this->metric->labels()['framework_version']);
    }
}
