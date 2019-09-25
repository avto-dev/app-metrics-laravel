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
            "{$metric_one->name()} Nan\n{$metric_two->name()} Nan",
            $this->formatter->format([$metric_one, $metric_two])
        );
    }

    /**
     * @return void
     */
    public function testFormatWithPassingMetricWithAllPossibleInterfaces(): void
    {
        $labels = [
            'foo' => 1,
            'bar' => 3.14,
            'baz' => 'yahoo',
        ];

        $mock = $this->getMetricMock('blah', true, 'fake_type', $labels, 'fake');

        $result = $this->formatter->format([$mock]);

        $this->assertSame(
            "# HELP blah fake\n# TYPE blah UNTYPED\nblah{foo=\"1\",bar=\"3.14\",baz=\"yahoo\"} 1",
            $result
        );
    }

    /**
     * @return void
     */
    public function testFormatType(): void
    {
        $data_sets = [
            ['counter', 'COUNTER'],
            ['COUNTER', 'COUNTER'],
            ['histogram', 'HISTOGRAM'],
            ['HISTOGRAM', 'HISTOGRAM'],
            ['gauge', 'GAUGE'],
            ['GAUGE', 'GAUGE'],
            ['summary', 'SUMMARY'],
            ['SUMMARY', 'SUMMARY'],
            ['foo', 'UNTYPED'],
            ['bar', 'UNTYPED'],
            ['untyped', 'UNTYPED'],
            ['', 'UNTYPED'],
        ];

        foreach ($data_sets as [$input, $expected]) {
            $mock = $this->getMetricMock('foo', true, $input);

            $result = $this->formatter->format([$mock]);

            $this->assertRegExp("~# TYPE foo {$expected}\n~", $result);
            $this->assertRegExp('~foo 1~', $result);
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

        foreach ($data_sets as [$input, $expected]) {
            $mock = $this->getMetricMock('foo', $input);

            $result = $this->formatter->format([$mock]);

            $this->assertSame("foo {$expected}", $result);
        }
    }

    /**
     * @return void
     */
    public function testFormatLabel(): void
    {
        $data_sets = [
            [[], ''],
            [['foo' => 'bar'], '{foo="bar"}'],
            [['foo' => 'Nan'], '{foo="Nan"}'],
            [['foo' => '-Inf'], '{foo="-Inf"}'],
            [['foo' => '+Inf'], '{foo="+Inf"}'],
            [['_foo' => 'bar'], '{_foo="bar"}'],
            [['123foo' => 'bar'], ''],
            [['foo' => 'ba\r'], '{foo="ba\\\r"}'],
            [['foo' => 'ba"r'], '{foo="ba\"r"}'],
            [['foo' => 'ba\nr'], '{foo="ba\\\nr"}'],
            [['foo' => 'bar', 'bar' => 'baz'], '{foo="bar",bar="baz"}'],
            [['foo' => false], '{foo="false"}'],
            [['foo' => true], '{foo="true"}'],
            [['foo' => null], ''],
            [['foo' => 123], '{foo="123"}'],
            [['foo' => 12.3], '{foo="12.3"}'],
            [['foo'], ''],
            [['foo' => \tmpfile()], ''],
            [['foo' => function () {
            }], ''],
            [['foo' => []], ''],
        ];

        foreach ($data_sets as [$input, $expected]) {
            $mock = $this->getMetricMock('foo', 1, null, $input);

            $result = $this->formatter->format([$mock]);

            $this->assertSame("foo{$expected} 1", $result);
        }
    }

    /**
     * @return void
     */
    public function testSetLineBreaker(): void
    {
        $data_sets = [
            "\n",
            \PHP_EOL,
            '',
            ' ',
            "\t",
            'some_string',
        ];
        $mock = $this->getMetricMock('foo', true, 'UNTYPED');

        foreach ($data_sets as $breaker) {
            $this->formatter->setLineBreaker($breaker);
            $result = $this->formatter->format([$mock]);

            $this->assertRegExp("~# TYPE foo UNTYPED{$breaker}~", $result);
        }
    }

    /**
     * @param string      $name
     * @param bool        $value
     * @param string|null $type
     * @param array|null  $labels
     * @param string|null $description
     *
     * @return MetricInterface
     */
    protected function getMetricMock(
        string $name = 'fake',
        $value = true,
        ?string $type = null,
        ?array $labels = null,
        ?string $description = null): MetricInterface
    {
        $interfaces = [MetricInterface::class];

        if ($type !== null) {
            $interfaces[] = HasTypeInterface::class;
        }

        if ($labels !== null) {
            $interfaces[] = HasLabelsInterface::class;
        }

        if ($description !== null) {
            $interfaces[] = HasDescriptionInterface::class;
        }

        $metric = m::mock(\implode(', ', $interfaces))
            ->makePartial()
            ->shouldReceive('name')
            ->andReturn($name)
            ->getMock()
            ->shouldReceive('value')
            ->andReturn($value)
            ->getMock();

        if ($type !== null) {
            $metric = $metric
                ->shouldReceive('type')
                ->andReturn($type)
                ->getMock();
        }

        if ($labels !== null) {
            $metric = $metric
                ->shouldReceive('labels')
                ->andReturn($labels)
                ->getMock();
        }

        if ($description !== null) {
            $metric = $metric
                ->shouldReceive('description')
                ->andReturn($description)
                ->getMock();
        }

        return $metric;
    }
}
