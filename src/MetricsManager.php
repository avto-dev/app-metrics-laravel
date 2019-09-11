<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Closure;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;
use AvtoDev\AppMetrics\Metrics\MetricInterface;

class MetricsManager implements MetricsManagerInterface
{
    /**
     * @var Closure[]
     */
    protected $factories = [];

    /**
     * @var string[]
     */
    protected $aliases = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * Create a new MetricsManager instance.
     *
     * @param Container $container
     * @param string[]  $metrics   Metric class names, e.g.: `[FooMetric::class, 'bar' => BarMetric::class]`
     *
     * @throws InvalidArgumentException If wrong metrics array passed
     */
    public function __construct(Container $container, array $metrics)
    {
        $this->container = $container;

        foreach ($metrics as $alias => $metric_class) {
            $this->addFactory($metric_class, \is_string($alias)
                ? $alias
                : null);
        }
    }

    /**
     * @param string      $metric_class
     * @param string|null $alias
     *
     * @throws InvalidArgumentException If passed wrong class name
     *
     * @return void
     */
    public function addFactory(string $metric_class, ?string $alias = null): void
    {
        if (! \class_exists($metric_class)) {
            throw new InvalidArgumentException("Class [{$metric_class}] does not exists");
        }

        if (! \in_array($contract = MetricInterface::class, \class_implements($metric_class), true)) {
            throw new InvalidArgumentException("Class [{$metric_class}] must implements [{$contract}]");
        }

        $this->factories[$metric_class] = function () use ($metric_class): MetricInterface {
            return $this->container->make($metric_class);
        };

        if (\is_string($alias)) {
            $this->aliases[$alias] = $metric_class;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function make(string $metric_abstract): MetricInterface
    {
        if ($this->exists($metric_abstract)) {
            return $this->factories[$metric_abstract]();
        }

        if ($this->aliasExists($metric_abstract)) {
            return $this->factories[$this->aliases[$metric_abstract]]();
        }

        throw new InvalidArgumentException("Unknown metric [{$metric_abstract}] requested");
    }

    /**
     * {@inheritdoc}
     */
    public function iterateAll(): iterable
    {
        foreach ($this->classes() as $class) {
            yield $this->make($class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $metric_class): bool
    {
        return isset($this->factories[$metric_class]);
    }

    /**
     * {@inheritdoc}
     */
    public function aliasExists(string $metric_alias): bool
    {
        return isset($this->aliases[$metric_alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function classes(): array
    {
        return \array_keys($this->factories);
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return \array_keys($this->aliases);
    }
}
