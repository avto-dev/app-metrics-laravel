<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Formatters;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Formatters\JsonFormatter;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\BarMetric;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\FooMetric;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;
use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;
use AvtoDev\AppMetrics\Formatters\UseCustomHttpHeadersInterface;

/**
 * @covers \AvtoDev\AppMetrics\Formatters\JsonFormatter<extended>
 */
class JsonFormatterTest extends AbstractUnitTestCase
{
    /**
     * @var JsonFormatter
     */
    protected $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = $this->app->make(JsonFormatter::class);
    }

    /**
     * @return void
     */
    public function testImplements(): void
    {
        $this->assertInstanceOf(MetricFormatterInterface::class, $this->formatter);
        $this->assertInstanceOf(UseCustomHttpHeadersInterface::class, $this->formatter);
    }

    /**
     * @return void
     */
    public function testHttpHeadersGetter(): void
    {
        $expected = [
            'Content-Type' => 'application/json',
        ];

        foreach ($expected as $name => $value) {
            $this->assertArrayHasKey($name, $this->formatter->httpHeaders());
            $this->assertSame($value, $this->formatter->httpHeaders()[$name]);
        }
    }

    /**
     * @return void
     */
    public function testFormatWithNothingPassed(): void
    {
        $this->assertSame('{}', $this->formatter->format([]));
    }

    /**
     * @return void
     */
    public function testFormatWithPassingOneMetric(): void
    {
        $metric_one = new FooMetric;
        $result     = \json_decode($this->formatter->format([$metric_one]), false);

        $this->assertObjectHasAttribute($name = $metric_one->name(), $result);
        $this->assertSame($metric_one->value(), $result->{$name}->value);
    }

    /**
     * @return void
     */
    public function testFormatWithPassingTwoMetrics(): void
    {
        $metric_one = new FooMetric;
        $metric_two = new BarMetric;
        $result     = \json_decode($this->formatter->format([$metric_one, $metric_two]), false);

        $this->assertObjectHasAttribute($name_one = $metric_one->name(), $result);
        $this->assertSame($metric_one->value(), $result->{$name_one}->value);

        $this->assertObjectHasAttribute($name_two = $metric_two->name(), $result);
        $this->assertSame($metric_two->value(), $result->{$name_two}->value);
    }

    /**
     * @return void
     */
    public function testFormatUsingMetricsCollection(): void
    {
        $collection = new class implements MetricsGroupInterface {
            public function metrics(): iterable
            {
                return [
                    new FooMetric,
                    new BarMetric,
                ];
            }
        };

        $result     = \json_decode($this->formatter->format([$collection]), false);

        $this->assertObjectHasAttribute($name_one = ($metric_one = new FooMetric)->name(), $result);
        $this->assertSame($metric_one->value(), $result->{$name_one}->value);

        $this->assertObjectHasAttribute($name_two = ($metric_two = new BarMetric)->name(), $result);
        $this->assertSame($metric_two->value(), $result->{$name_two}->value);
    }

    /**
     * @return void
     */
    public function testFormatUsingMetricsCollectionAndOneMetric(): void
    {
        $collection = new class implements MetricsGroupInterface {
            public function metrics(): iterable
            {
                return [
                    new FooMetric,
                ];
            }
        };

        $result     = \json_decode($this->formatter->format([$collection, $metric = new BarMetric]), false);

        $this->assertObjectHasAttribute($name_one = ($metric_one = new FooMetric)->name(), $result);
        $this->assertSame($metric_one->value(), $result->{$name_one}->value);

        $this->assertObjectHasAttribute($name_two = $metric->name(), $result);
        $this->assertSame($metric->value(), $result->{$name_two}->value);
    }

    /**
     * @return void
     */
    public function testFormatWithPassingMetricWithAllPossibleInterfaces(): void
    {
        $metric = new class implements
            MetricInterface,
            HasDescriptionInterface,
            HasLabelsInterface,
            HasTypeInterface {
            public function description(): string
            {
                return 'fake';
            }

            public function labels(): array
            {
                return [
                    'foo' => 1,
                    'bar' => \M_PI,
                    'baz' => 'yahoo',
                ];
            }

            public function name(): string
            {
                return 'blah';
            }

            public function type(): string
            {
                return 'fake_type';
            }

            public function value()
            {
                return true;
            }
        };

        $result = \json_decode($this->formatter->format([$metric]), false);

        $this->assertObjectHasAttribute($name = $metric->name(), $result);
        $this->assertSame($metric->value(), $result->{$name}->value);
        $this->assertSame($metric->description(), $result->{$name}->description);
        $this->assertEquals((object) $metric->labels(), $result->{$name}->labels);
        $this->assertSame($metric->type(), $result->{$name}->type);
    }
}
