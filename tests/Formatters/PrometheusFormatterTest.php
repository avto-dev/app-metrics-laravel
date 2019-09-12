<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Formatters;

use Mockery as m;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\BarMetric;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\FooMetric;
use AvtoDev\AppMetrics\Formatters\PrometheusFormatter;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;
use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;
use AvtoDev\AppMetrics\Formatters\UseCustomHttpHeadersInterface;

/**
 * @covers \AvtoDev\AppMetrics\Formatters\PrometheusFormatter<extended>
 */
class PrometheusFormatterTest extends AbstractUnitTestCase
{
    /**
     * @var PrometheusFormatter
     */
    protected $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = $this->app->make(PrometheusFormatter::class);
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
            'Content-Type' => 'text/plain',
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
        $this->assertSame('', $this->formatter->format([]));
    }

    /**
     * @return void
     */
    public function testFormatWithPassingOneMetric(): void
    {
        $metric = new FooMetric;

        $this->assertSame("{$metric->name()} Nan", $this->formatter->format([$metric]));
    }

    /**
     * @return void
     */
    public function testFormatWithPassingTwoMetric(): void
    {
        $metric_one = new FooMetric;
        $metric_two = new BarMetric;

        $this->assertSame(
            "{$metric_one->name()} Nan\n{$metric_two->name()} {$metric_two->value()}",
            $this->formatter->format([$metric_one, $metric_two])
        );
    }

    /**
     * @return void
     *
     * @todo Improve this test, add tests for protected methods
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
                    'bar' => 3.14,
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

        $result = $this->formatter->format([$metric]);

        $this->assertRegExp("~HELP {$metric->name()} {$metric->description()}\n~", $result);
        $this->assertRegExp("~TYPE {$metric->name()} UNTYPED\n~", $result);
        $this->assertRegExp("~{$metric->name()}{foo=\"1\",bar=\"3.14\",baz=\"yahoo\"} 1~", $result);
    }

    /**
     * @return void
     */
    public function testFormatType(): void
    {
        $data_sets = [
            ['counter', HasTypeInterface::TYPE_COUNTER],
            ['COUNTER', HasTypeInterface::TYPE_COUNTER],
            ['histogram', HasTypeInterface::TYPE_HISTOGRAM],
            ['HISTOGRAM', HasTypeInterface::TYPE_HISTOGRAM],
            ['gauge', HasTypeInterface::TYPE_GAUGE],
            ['GAUGE', HasTypeInterface::TYPE_GAUGE],
            ['summary', HasTypeInterface::TYPE_SUMMARY],
            ['SUMMARY', HasTypeInterface::TYPE_SUMMARY],
            ['foo', HasTypeInterface::TYPE_UNTYPED],
            ['bar', HasTypeInterface::TYPE_UNTYPED],
            ['untyped', HasTypeInterface::TYPE_UNTYPED],
        ];

        $mock = m::mock(\implode(', ', [MetricInterface::class, HasTypeInterface::class]))
            ->makePartial()
            ->shouldReceive('name')
            ->andReturn('foo')
            ->getMock()
            ->shouldReceive('value')
            ->andReturn(true)
            ->getMock();

        foreach ($data_sets as [$input, $expected]) {
            $mock = $mock
                ->shouldReceive('type')
                ->once()
                ->andReturn($input)
                ->getMock();

            $result = $this->formatter->format([$mock]);

            $this->assertRegExp("~TYPE {$mock->name()} {$expected}\n~", $result);
            $this->assertRegExp("~{$mock->name()} 1~", $result);
        }
    }

    /**
     * @return void
     */
    public function testFormatValue(): void
    {
        $data_sets = [
            [1.2, '1.2'],
            [1, '1'],
            [true, '1'],
            [false, '0'],
            ['123', '123'],
            ['12foo', 'Nan'],
            [['10', '20'], 'Nan'],
            [null, 'Nan'],
            ['Nan', 'Nan'],
            ['+Inf', '+Inf'],
            ['-Inf', '-Inf'],
        ];

        $mock = m::mock(MetricInterface::class)
            ->makePartial()
            ->shouldReceive('name')
            ->andReturn('foo')
            ->getMock();

        foreach ($data_sets as [$input, $expected]) {
            $mock = $mock
                ->shouldReceive('value')
                ->once()
                ->andReturn($input)
                ->getMock();

            $result = $this->formatter->format([$mock]);

            $this->assertSame("{$mock->name()} {$expected}", $result);
        }
    }

    /**
     * @return void
     */
    public function testFormatLabel(): void
    {
        $data_sets = [
            [[], ''],
            [['foo' => 'bar'], 'foo="bar"'],
            [['foo' => 'bar', 'bar' => 'baz'], 'foo="bar",bar="baz"'],
            [['foo'=>false], 'foo=""'],
            [['foo'=>null], ''],
            [['foo'=>123], 'foo="123"'],
            [['foo'=>12.3], 'foo="12.3"'],
            [['foo'], ''],
        ];

        $mock = m::mock(\implode(', ', [MetricInterface::class, HasLabelsInterface::class]))
            ->makePartial()
            ->shouldReceive('name')
            ->andReturn('foo')
            ->getMock()
            ->shouldReceive('value')
            ->andReturn(1)
            ->getMock();

        foreach ($data_sets as [$input, $expected]) {
            $mock = $mock
                ->shouldReceive('labels')
                ->once()
                ->andReturn($input)
                ->getMock();

            $result = $this->formatter->format([$mock]);

            $this->assertRegExp("~{$mock->name()}{{$expected}} 1~", $result);
        }
    }
}
