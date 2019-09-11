<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Metrics;

class HostInfoMetric implements MetricInterface, HasLabelsInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'host_info';
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function labels(): array
    {
        return [
            'hostname' => \php_uname('n'),
            'version'  => \php_uname('v'),
            'arch'     => \php_uname('m'),
        ];
    }
}
