<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;
use AvtoDev\AppMetrics\Traits\WithThrowableReportingTrait;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface;

class JsonFormatter implements MetricFormatterInterface, UseCustomHttpHeadersInterface
{
    use WithThrowableReportingTrait;

    /**
     * Create json formatter.
     *
     * @param ExceptionHandler $exception_handler
     */
    public function __construct(ExceptionHandler $exception_handler)
    {
        $this->exception_handler = $exception_handler;
    }

    /**
     * {@inheritdoc}
     */
    public function httpHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param int $options Encoding options
     *
     * @return string Json-object (in string representation)
     */
    public function format(iterable $metrics, int $options = \JSON_UNESCAPED_UNICODE): string
    {
        $result = [];

        foreach ($metrics as $metric) {
            try {
                if ($metric instanceof MetricsGroupInterface) {
                    foreach ($metric->metrics() as $collection_item) {
                        if ($collection_item instanceof MetricInterface) {
                            $result[] = (object) $this->metricToArray($collection_item);
                        }
                    }
                } elseif ($metric instanceof MetricInterface) {
                    $result[] = (object) $this->metricToArray($metric);
                }
            } catch (ShouldBeSkippedMetricExceptionInterface $e) {
                $this->reportThrowable($e);
            }
        }

        return (string) \json_encode($result, $options);
    }

    /**
     * @param MetricInterface $metric
     *
     * @return array
     */
    protected function metricToArray(MetricInterface $metric): array
    {
        $result = [
            'name'  => $metric->name(),
            'value' => $metric->value(),
        ];

        if ($metric instanceof HasDescriptionInterface) {
            $result['description'] = $metric->description();
        }

        if ($metric instanceof HasLabelsInterface) {
            $result['labels'] = $metric->labels();
        }

        if ($metric instanceof HasTypeInterface) {
            $result['type'] = $metric->type();
        }

        return $result;
    }
}
