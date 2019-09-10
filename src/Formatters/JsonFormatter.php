<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;

class JsonFormatter implements MetricFormatterInterface, UseCustomHttpHeadersInterface
{
    /**
     * {@inheritDoc}
     */
    public function httpHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @param int $options Encoding options
     *
     * @return string Json-object (in string representation)
     */
    public function format(iterable $metrics, int $options = \JSON_UNESCAPED_UNICODE): string
    {
        $result = [];

        foreach ($metrics as $metric) {
            if ($metric instanceof MetricInterface) {
                $item = [
                    'value' => $metric->value(),
                ];

                if ($metric instanceof HasDescriptionInterface) {
                    $item['description'] = $metric->description();
                }

                if ($metric instanceof HasLabelsInterface) {
                    $item['labels'] = $metric->labels();
                }

                if ($metric instanceof HasTypeInterface) {
                    $item['type'] = $metric->type();
                }

                $result[$metric->name()] = (object) $item;
            }
        }

        return (string) \json_encode((object) $result, $options);
    }
}
