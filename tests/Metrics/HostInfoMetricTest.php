<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Metrics;

use AvtoDev\AppMetrics\Metrics\HostInfoMetric;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;

/**
 * @covers \AvtoDev\AppMetrics\Metrics\HostInfoMetric<extended>
 */
class HostInfoMetricTest extends AbstractUnitTestCase
{
    /**
     * @var HostInfoMetric
     */
    protected $metric;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->metric = $this->app->make(HostInfoMetric::class);
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
        $this->assertSame('host_info', $this->metric->name());
        $this->assertSame(1, $this->metric->value());
        $this->assertSame(\php_uname('n'), $this->metric->labels()['hostname']);
        $this->assertSame(\php_uname('v'), $this->metric->labels()['version']);
        $this->assertSame(\php_uname('m'), $this->metric->labels()['arch']);
    }
}
