<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Formatters;

use Illuminate\Support\Str;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\Metrics\HasTypeInterface;
use AvtoDev\AppMetrics\Metrics\HasLabelsInterface;
use AvtoDev\AppMetrics\Metrics\HasDescriptionInterface;
use AvtoDev\AppMetrics\Formatters\Dictionaries\PrometheusValuesDictionary;

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
     *
     * @example
     * formatValue(1.2);            // '1.2'
     * formatValue(1);              // '1'
     * formatValue(true);           // '1'
     * formatValue(false);          // '0'
     * formatValue('123');          // '123'
     * formatValue('12foo');        // 'Nan'
     * formatValue(['10', '20']);   // 'Nan'
     * formatValue(null);           // 'Nan'
     * formatValue('Nan');          // 'Nan'
     * formatValue('+Inf');         // '+Inf'
     * formatValue('-Inf');         // '-Inf'
     */
    protected function formatValue($value): string
    {
        if (\is_int($value) || \is_float($value)) {
            return (string) $value;
        }

        if (\is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (
            \is_string($value)
            && \in_array(
                Str::upper($value),
                \array_map([Str::class, 'upper'], PrometheusValuesDictionary::all()),
                true
            )
        ) {
            return $value;
        }

        if (\is_string($value) && \preg_match('/^\d*$/', $value)) {
            return $value;
        }

        return PrometheusValuesDictionary::NAN;
    }

    /**
     * @param string $type
     *
     * @return string
     *
     * @example
     * formatType('counter');   // 'COUNTER'
     * formatType('COUNTER');   // 'COUNTER'
     * formatType('gauge');     // 'GAUGE'
     * formatType('histogram'); // 'HISTOGRAM'
     * formatType('summary');   // 'SUMMARY'
     * formatType('untyped');   // 'UNTYPED'
     * formatType('foo');       // 'UNTYPED'
     * formatType('');          // 'UNTYPED'
     */
    protected function formatType(string $type): string
    {
        switch ($type = Str::upper($type)) {
            case HasTypeInterface::TYPE_COUNTER:
            case HasTypeInterface::TYPE_GAUGE:
            case HasTypeInterface::TYPE_HISTOGRAM:
            case HasTypeInterface::TYPE_SUMMARY:
                return $type;
        }

        return HasTypeInterface::TYPE_UNTYPED;
    }

    /**
     * @param array $labels
     *
     * @return string
     *
     * @example
     * formatLabels([])                               // ''
     * formatLabels(['foo' => 'bar'])                 // '{foo="bar"}'
     * formatLabels(['foo' => 'Nan'])                 // '{foo="Nan"}'
     * formatLabels(['foo' => '-Inf'])                // '{foo="-Inf"}'
     * formatLabels(['foo' => '+Inf'])                // '{foo="+Inf"}'
     * formatLabels(['_foo' => 'bar'])                // '{_foo="bar"}'
     * formatLabels(['123foo' => 'bar'])              // ''
     * formatLabels(['foo' => 'ba\r'])                // '{foo="ba\\\r"}'
     * formatLabels(['foo' => 'ba"r'])                // '{foo="ba\"r"}'
     * formatLabels(['foo' => 'ba\nr'])               // '{foo="ba\\\nr"}'
     * formatLabels(['foo' => 'bar', 'bar' => 'baz']) // '{foo="bar",bar="baz"}'
     * formatLabels(['foo' => false])                 // '{foo="false"}'
     * formatLabels(['foo' => true])                  // '{foo="true"}'
     * formatLabels(['foo' => null])                  // ''
     * formatLabels(['foo' => 123])                   // '{foo="123"}'
     * formatLabels(['foo' => 12.3])                  // '{foo="12.3"}'
     * formatLabels(['foo'])                          // ''
     * formatLabels(['foo' => \tmpfile()])            // ''
     * formatLabels(['foo' => function(){}])          // ''
     * formatLabels(['foo' => []])                    // ''
     */
    protected function formatLabels(array $labels): string
    {
        $labels_array = [];

        foreach ($labels as $key => $value) {
            if (! \is_scalar($value) || empty($key)) {
                continue;
            }
            /**
             * @link https://prometheus.io/docs/concepts/data_model/#metric-names-and-labels
             */
            if (! \preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string) $key)) {
                continue;
            }

            $formatted_value = $value;

            if (\is_bool($value)) {
                $formatted_value = $value ? 'true' : 'false';
            }

            if (\is_int($value) || \is_float($value)) {
                $formatted_value = (string) $value;
            }

            $labels_array[$key] = \addslashes((string) $formatted_value);
        }

        $formatted_labels = \implode(',', \array_filter(\array_map(
            static function ($value, $key) {
                return  \sprintf('%s="%s"', $key, $value);
            },
            $labels_array,
            \array_keys($labels_array)
        )));

        return $formatted_labels === '' ? $formatted_labels : '{' . $formatted_labels . '}';
    }
}
