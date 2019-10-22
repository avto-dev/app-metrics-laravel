<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Formatters;

use Mockery as m;
use RuntimeException;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use AvtoDev\AppMetrics\Formatters\JsonFormatter;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\BarMetric;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\FooMetric;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;
use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;
use AvtoDev\AppMetrics\Formatters\UseCustomHttpHeadersInterface;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricException;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface;

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
        $this->assertSame('[]', $this->formatter->format([]));
    }

    /**
     * @return void
     */
    public function testFormatWithPassingOneMetric(): void
    {
        $metric_one = new FooMetric;
        $result     = \json_decode($this->formatter->format([$metric_one]), false);

        $this->assertCount(1, $result);
        $this->assertSame($metric_one->name(), $result[0]->name);
        $this->assertSame($metric_one->value(), $result[0]->value);
    }

    /**
     * @return void
     */
    public function testFormatWithPassingTwoMetrics(): void
    {
        $metric_one = new FooMetric;
        $metric_two = new BarMetric;
        $result     = \json_decode($this->formatter->format([$metric_one, $metric_two]), false);

        $this->assertCount(2, $result);

        $this->assertSame($metric_one->name(), $result[0]->name);
        $this->assertSame($metric_one->value(), $result[0]->value);

        $this->assertSame($metric_two->name(), $result[1]->name);
        $this->assertSame($metric_two->value(), $result[1]->value);
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
                    $metric_one = new FooMetric,
                    $metric_two = new BarMetric,
                ];
            }
        };

        $result     = \json_decode($this->formatter->format([$collection]), false);
        $metrics    = $collection->metrics();

        $this->assertCount(2, $result);

        $this->assertSame($metrics[0]->name(), $result[0]->name);
        $this->assertSame($metrics[0]->value(), $result[0]->value);

        $this->assertSame($metrics[1]->name(), $result[1]->name);
        $this->assertSame($metrics[1]->value(), $result[1]->value);
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
        $metrics    = $collection->metrics();

        $this->assertCount(2, $result);

        $this->assertSame($metrics[0]->name(), $result[0]->name);
        $this->assertSame($metrics[0]->value(), $result[0]->value);

        $this->assertSame($metric->name(), $result[1]->name);
        $this->assertSame($metric->value(), $result[1]->value);
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

        $this->assertCount(1, $result);
        $this->assertSame($metric->name(), $result[0]->name);
        $this->assertSame($metric->value(), $result[0]->value);
        $this->assertSame($metric->description(), $result[0]->description);
        $this->assertEquals((object) $metric->labels(), $result[0]->labels);
        $this->assertSame($metric->type(), $result[0]->type);
    }

    /**
     * @return void
     */
    public function testFormatWithShouldBeSkippedException(): void
    {
        $exception_handler = m::mock(ExceptionHandler::class)
            ->shouldReceive('report')
            ->with(m::on(static function ($argument) {
                return $argument instanceof ShouldBeSkippedMetricExceptionInterface
                       && $argument->getMessage() === 'This metric should be skipped';
            }))
            ->getMock();

        $this->formatter = new JsonFormatter($exception_handler);

        $metric = new class implements MetricInterface {
            public function name(): string
            {
                return 'blah';
            }

            public function value()
            {
                throw new ShouldBeSkippedMetricException('This metric should be skipped');
            }
        };

        $result = $this->formatter->format([$metric]);

        $this->assertSame('[]', $result);
    }

    /**
     * @return void
     */
    public function testFormatWithAnyOtherException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Something went wrong');

        $metric = new class implements MetricInterface {
            public function name(): string
            {
                return 'blah';
            }

            public function value()
            {
                throw new RuntimeException('Something went wrong');
            }
        };

        $this->formatter->format([$metric]);
    }
}
