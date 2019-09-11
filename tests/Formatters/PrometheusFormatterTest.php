<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Formatters;

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

        $this->assertSame("{$metric->name()} {$metric->value()}", $this->formatter->format([$metric]));
    }

    /**
     * @return void
     */
    public function testFormatWithPassingTwoMetric(): void
    {
        $metric_one = new FooMetric;
        $metric_two = new BarMetric;

        $this->assertSame(
            "{$metric_one->name()} {$metric_one->value()}\n{$metric_two->name()} {$metric_two->value()}",
            $this->formatter->format([$metric_one, $metric_two])
        );
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
}
