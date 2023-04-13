<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Closure;
use LogicException;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;
use AvtoDev\AppMetrics\Formatters\MetricFormatterInterface;

class FormattersManager implements FormattersManagerInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Closure[]
     */
    protected $factories;

    /**
     * @var string|null
     */
    protected $default_format;

    /**
     * Create a new MetricsManager instance.
     *
     * @param Container   $container
     * @param string[]    $formatters     Formatter classes, e.g.: `['foo' => FooFormatter::class, 'bar' =>
     *                                    BarFormatter::class]`
     * @param string|null $default_format Formatter alias, used by default
     */
    public function __construct(Container $container, array $formatters, ?string $default_format = null)
    {
        $this->container = $container;

        if (\is_string($default_format) && \array_key_exists($default_format, $formatters)) {
            $this->default_format = $default_format;
        }

        foreach ($formatters as $alias => $class_name) {
            $this->addFactory($alias, $class_name);
        }
    }

    /**
     * @param string $alias
     * @param string $formatter_class
     *
     * @throws InvalidArgumentException If passed wrong class name
     *
     * @return void
     */
    public function addFactory(string $alias, string $formatter_class): void
    {
        if (! \class_exists($formatter_class)) {
            throw new InvalidArgumentException("Class [{$formatter_class}] does not exists");
        }

        if (! \is_array(\class_implements($formatter_class)) || ! \in_array($contract = MetricFormatterInterface::class, \class_implements($formatter_class), true)) {
            $interface_name = $contract ?? MetricFormatterInterface::class;
            throw new InvalidArgumentException("Class [{$formatter_class}] must implements [{$interface_name}]");
        }

        $this->factories[$alias] = function () use ($formatter_class): MetricFormatterInterface {
            /** @var MetricFormatterInterface $formatter */
            $formatter = $this->container->make($formatter_class);

            return $formatter;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function make(string $alias): MetricFormatterInterface
    {
        if ($this->exists($alias)) {
            return $this->factories[$alias]();
        }

        throw new InvalidArgumentException("Unknown formatter [{$alias}] requested");
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $alias): bool
    {
        return isset($this->factories[$alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return \array_keys($this->factories);
    }

    /**
     * {@inheritdoc}
     */
    public function default(): MetricFormatterInterface
    {
        if (\is_string($this->default_format)) {
            return $this->make($this->default_format);
        }

        throw new LogicException('Default formatter was not set');
    }
}
