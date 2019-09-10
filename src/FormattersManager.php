<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Illuminate\Contracts\Container\Container;

class FormattersManager implements FormattersManagerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string[]
     */
    protected $formatters;

    /**
     * @var string
     */
    protected $default_format;

    /**
     * Create a new MetricsManager instance.
     *
     * @param Container $container
     * @param string[]  $formatters        Formatter classes, e.g.: `['foo' => FooFormatter::class, 'bar' =>
     *                                     BarFormatter::class]`
     * @param string    $default_format    Formatter alias, used by default
     */
    public function __construct(Container $container, array $formatters, string $default_format)
    {
        $this->container      = $container;
        $this->formatters     = $formatters;
        $this->default_format = $default_format;
    }

    /**
     * @return string[]
     */
    public function formatters(): array
    {
        return $this->formatters;
    }
}
