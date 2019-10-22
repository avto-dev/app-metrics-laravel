<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Closure;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;
use AvtoDev\AppMetrics\Traits\ThrowableToExceptionTrait;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface;

class MetricsManager implements MetricsManagerInterface
{
    use ThrowableToExceptionTrait;

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
     * @var ExceptionHandler
     */
    protected $exception_handler;

    /**
     * Create a new metrics manager instance.
     *
     * @param Container        $container
     * @param string[]         $metrics           Metric class names, e.g.: `[FooMetric::class, 'bar' => BarMetric::class,
     *                                            'blah' => Metrics\MetricsGroup::class]`
     * @param ExceptionHandler $exception_handler
     */
    public function __construct(Container $container, array $metrics, ExceptionHandler $exception_handler)
    {
        $this->container         = $container;
        $this->exception_handler = $exception_handler;

        foreach ($metrics as $alias => $metric_or_group_class) {
            $this->addFactory($metric_or_group_class, \is_string($alias)
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
        static $required_interfaces = [
            MetricInterface::class,
            MetricsGroupInterface::class,
        ];

        if (! \class_exists($metric_class)) {
            throw new InvalidArgumentException("Class [{$metric_class}] does not exists");
        }

        if (empty(\array_intersect(\class_implements($metric_class), $required_interfaces))) {
            throw new InvalidArgumentException(
                "Class [{$metric_class}] must implements one of [" . \implode('|', $required_interfaces) . ']'
            );
        }

        $this->factories[$metric_class] = function () use ($metric_class) {
            return $this->container->make($metric_class);
        };

        if (\is_string($alias)) {
            $this->aliases[$alias] = $metric_class;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function make(string $metric_abstract)
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
        return $this->iterate($this->classes());
    }

    /**
     * {@inheritdoc}
     */
    public function iterate(array $abstracts): iterable
    {
        foreach ($abstracts as $alias) {
            try {
                /** @var MetricInterface|MetricsGroupInterface $item */
                $item = $this->make($alias);

                if ($item instanceof MetricInterface) {
                    yield $item;
                } elseif ($item instanceof MetricsGroupInterface) {
                    foreach ($item->metrics() as $metric) {
                        yield $metric;
                    }
                }
            } catch (ShouldBeSkippedMetricExceptionInterface $e) {
                $this->exception_handler->report($this->convertThrowableToException($e));
            }
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
