<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Metrics;

class HostInfoMetric implements MetricInterface, HasLabelsInterface
{
    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'host_info';
    }

    /**
     * {@inheritDoc}
     */
    public function value()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
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
