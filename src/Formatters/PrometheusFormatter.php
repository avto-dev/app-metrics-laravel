<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

use Illuminate\Support\Str;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;

class PrometheusFormatter implements MetricFormatterInterface, UseCustomHttpHeadersInterface
{
    /**
     * @var string
     */
    protected $new_line = \PHP_EOL;

    /**
     * @param string $nl
     */
    public function setLineBreaker(string $nl): void
    {
        $this->new_line = $nl;
    }

    /**
     * {@inheritdoc}
     */
    public function httpHeaders(): array
    {
        return [
            'Content-Type' => 'text/plain',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function format(iterable $metrics): string
    {
        $result = '';

        foreach ($metrics as $metric) {
            if ($metric instanceof MetricInterface) {
                if ($metric instanceof HasDescriptionInterface) {
                    $result .= "HELP {$metric->name()} {$metric->description()}" . $this->new_line;
                }

                if ($metric instanceof HasTypeInterface) {
                    $result .= "TYPE {$metric->name()} {$this->formatType($metric->type())}" . $this->new_line;
                }

                $labels_string = $metric instanceof HasLabelsInterface
                    ? $this->formatLabels($metric->labels())
                    : '';

                $result .= "{$metric->name()}{$labels_string} {$this->formatValue($metric->value())}" . $this->new_line;
            }
        }

        if (Str::endsWith($result, $this->new_line)) {
            $result = Str::replaceLast($this->new_line, '', $result);
        }

        return $result;
    }

    /**
     * @param string|int|float|bool|null|array $value
     *
     * @return string
     */
    protected function formatValue($value): string
    {
        return is_array($value)
            ? \implode(' ', $value)
            : (string) $value;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function formatType(string $type): string
    {
        switch ($type) {
            case HasTypeInterface::TYPE_COUNTER:
                return 'COUNTER';

            case HasTypeInterface::TYPE_GAUGE:
                return 'GAUGE';

            case HasTypeInterface::TYPE_HISTOGRAM:
                return 'HISTOGRAM';

            case HasTypeInterface::TYPE_SUMMARY:
                return 'SUMMARY';
        }

        return 'UNTYPED';
    }

    /**
     * @param array $labels
     *
     * @return string
     */
    protected function formatLabels(array $labels): string
    {
        return '{' . \implode(',', \array_filter(\array_map(
                static function ($value, $key) {
                    return \is_scalar($value) && ! empty($key)
                        ? \sprintf('%s="%s"', $key, \addslashes((string) $value))
                        : null;
                },
                $labels,
                \array_keys($labels)
            ))) . '}';
    }
}
