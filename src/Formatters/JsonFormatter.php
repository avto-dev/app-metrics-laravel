<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;

class JsonFormatter implements MetricFormatterInterface, UseCustomHttpHeadersInterface
{
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
            if ($metric instanceof MetricsGroupInterface) {
                foreach ($metric->metrics() as $collection_item) {
                    if ($collection_item instanceof MetricInterface) {
                        $result[$collection_item->name()] = (object) $this->metricToArray($collection_item);
                    }
                }
            } elseif ($metric instanceof MetricInterface) {
                $result[$metric->name()] = (object) $this->metricToArray($metric);
            }
        }

        return (string) \json_encode((object) $result, $options);
    }

    /**
     * @param MetricInterface $metric
     *
     * @return array
     */
    protected function metricToArray(MetricInterface $metric): array
    {
        $result = [
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
