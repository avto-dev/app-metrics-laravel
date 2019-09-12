<?php

namespace AvtoDev\AppMetrics\Metrics;

interface HasTypeInterface
{
    /**
     * Cumulative metric that represents a single monotonically increasing counter.
     */
    public const TYPE_COUNTER = 'COUNTER';

    /**
     * Represents a single numerical value that can arbitrarily go up and down.
     */
    public const TYPE_GAUGE = 'GAUGE';

    /**
     * Observations (usually things like request durations or response sizes).
     */
    public const TYPE_HISTOGRAM = 'HISTOGRAM';

    /**
     * Similar to a histogram, a summary samples observations (usually things like request durations and response
     * sizes).
     */
    public const TYPE_SUMMARY = 'SUMMARY';

    /**
     * Safe fallback type
     */
    public const TYPE_UNTYPED = 'UNTYPED';

    /**
     * Get metric type.
     *
     * Note: Type should be one of supported values.
     *
     * @return string
     */
    public function type(): string;
}
