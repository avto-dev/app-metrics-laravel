<?php

namespace AvtoDev\AppMetrics\Formatters;

use AvtoDev\AppMetrics\Metrics\MetricInterface;

interface MetricFormatterInterface
{
    /**
     * Represent passed metrics array in some format.
     *
     * @param MetricInterface[]|iterable $metrics
     *
     * @return mixed
     */
    public function format(iterable $metrics);
}
