<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Formatters\Dictionaries;

class PrometheusValuesDictionary
{
    /**
     * Not a number value.
     *
     * @var string
     */
    public const NAN = 'Nan';

    /**
     * Positive infinity (+Inf) value.
     *
     * @var string
     */
    public const POSITIVE_INF = '+Inf';

    /**
     * Negative infinity (-Inf) value.
     *
     * @var string
     */
    public const NEGATIVE_INF = '-Inf';

    /**
     * Returns all dictionary entries.
     *
     * @return array
     */
    public static function all(): array
    {
//        return (new \ReflectionClass(static::class))->getConstants();

        return [
            static::NAN,
            static::POSITIVE_INF,
            static::NEGATIVE_INF,
        ];
    }
}
