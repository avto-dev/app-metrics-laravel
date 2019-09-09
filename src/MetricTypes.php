<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

class MetricTypes
{
    /**
     * Cumulative metric that represents a single monotonically increasing counter.
     */
    public const COUNTER = 'counter';

    /**
     * Represents a single numerical value that can arbitrarily go up and down.
     */
    public const GAUGE = 'gauge';

    /**
     * Observations (usually things like request durations or response sizes).
     */
    public const HISTOGRAM = 'histogram';

    /**
     * Similar to a histogram, a summary samples observations (usually things like request durations and response
     * sizes).
     */
    public const SUMMARY = 'summary';
}
