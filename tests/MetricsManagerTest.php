<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests;

use Generator;
use Mockery as m;
use stdClass;
use Illuminate\Support\Str;
use InvalidArgumentException;
use AvtoDev\AppMetrics\MetricsManager;
use AvtoDev\AppMetrics\MetricsManagerInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\FooGroup;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\BarMetric;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\FooMetric;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\SkippingMetric;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface;

/**
 * @covers \AvtoDev\AppMetrics\MetricsManager<extended>
 */
class MetricsManagerTest extends AbstractUnitTestCase
{
    /**
     * @var MetricsManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new MetricsManager(
            $this->app,
            [
                'foo'     => FooMetric::class,
                BarMetric::class,
                'grouped' => FooGroup::class,
            ],
            $this->app->make(ExceptionHandler::class)
        );
    }

    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(MetricsManagerInterface::class, $this->manager);
    }

    /**
     * @return void
     */
    public function testMake(): void
    {
        $this->assertInstanceOf(FooMetric::class, $this->manager->make(FooMetric::class));
        $this->assertInstanceOf(FooMetric::class, $this->manager->make('foo'));
        $this->assertInstanceOf(BarMetric::class, $this->manager->make(BarMetric::class));
        $this->assertInstanceOf(FooGroup::class, $this->manager->make(FooGroup::class));
        $this->assertInstanceOf(FooGroup::class, $this->manager->make('grouped'));

        $this->assertSame('foo value', $this->manager->make(FooMetric::class)->value());
        $this->assertSame('foo', $this->manager->make(FooMetric::class)->name());

        $this->assertSame('bar value', $this->manager->make(BarMetric::class)->value());
        $this->assertSame('bar', $this->manager->make(BarMetric::class)->name());

        $this->assertSame('foo value', $this->manager->make('foo')->value());
        $this->assertSame('foo', $this->manager->make('foo')->name());
    }

    /**
     * @return void
     */
    public function testAddFactory(): void
    {
        $this->manager = new MetricsManager($this->app, [], $this->app->make(ExceptionHandler::class));

        $this->assertFalse($this->manager->exists(BarMetric::class));
        $this->assertFalse($this->manager->aliasExists($alias = Str::random()));

        $this->manager->addFactory(BarMetric::class, $alias);

        $this->assertInstanceOf(BarMetric::class, $this->manager->make(BarMetric::class));
        $this->assertInstanceOf(BarMetric::class, $this->manager->make($alias));

        $this->assertFalse($this->manager->exists(FooMetric::class));

        $this->manager->addFactory(FooMetric::class);
        $this->assertInstanceOf(FooMetric::class, $this->manager->make(FooMetric::class));

        $this->assertFalse($this->manager->exists(FooGroup::class));

        $this->manager->addFactory(FooGroup::class);
        $this->assertInstanceOf(FooGroup::class, $this->manager->make(FooGroup::class));
    }

    /**
     * @return void
     */
    public function testMakeWithSkippingMetric(): void
    {
        $this->expectException(ShouldBeSkippedMetricExceptionInterface::class);
        $this->expectExceptionMessage('Metric should be skipped');

        $this->manager->addFactory(SkippingMetric::class);
        $this->manager->make(SkippingMetric::class);
    }

    /**
     * @return void
     */
    public function testIterateWithSkippingMetric(): void
    {
        $exception_handler = m::mock(ExceptionHandler::class)
            ->shouldReceive('report')
            ->with(m::on(static function ($argument) {
                return $argument instanceof ShouldBeSkippedMetricExceptionInterface
                    && $argument->getMessage() === 'Metric should be skipped';
            }))
            ->getMock();

        $this->manager = new MetricsManager($this->app, [], $exception_handler);

        $this->manager->addFactory(SkippingMetric::class);

        /** @var Generator $iterable */
        $iterable = $this->manager->iterate([SkippingMetric::class]);
        $iterable->current();
    }

    /**
     * @return void
     */
    public function testAddFactoryUsingNotExistingClassName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('~class.*not.*exists~i');

        $this->manager->addFactory(Str::random());
    }

    /**
     * @return void
     */
    public function testAddFactoryUsingWrongClassName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('~class.*must.*implements~i');

        $this->manager->addFactory(\stdClass::class);
    }

    /**
     * @return void
     */
    public function testMakeThrowsAnExceptionWhenPassedWrongAlias(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->manager->make(Str::random());
    }

    /**
     * @return void
     */
    public function testMakeThrowsAnExceptionWhenPassedWrongClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->manager->make(stdClass::class);
    }

    /**
     * @return void
     */
    public function testExists(): void
    {
        $this->assertTrue($this->manager->exists(FooMetric::class));
        $this->assertTrue($this->manager->exists(BarMetric::class));
        $this->assertTrue($this->manager->exists(FooGroup::class));
        $this->assertTrue($this->manager->aliasExists('foo'));
        $this->assertTrue($this->manager->aliasExists('grouped'));

        $this->assertFalse($this->manager->aliasExists(FooMetric::class));
        $this->assertFalse($this->manager->aliasExists(BarMetric::class));
        $this->assertFalse($this->manager->aliasExists(FooGroup::class));
        $this->assertFalse($this->manager->exists(Str::random()));
        $this->assertFalse($this->manager->exists(stdClass::class));
    }

    /**
     * @return void
     */
    public function testCustomIterator(): void
    {
        $all = [];
        \array_push($all, ...$this->manager->iterate(['foo', BarMetric::class]));

        $this->assertInstanceOf(FooMetric::class, $all[0]);
        $this->assertInstanceOf(BarMetric::class, $all[1]);

        $this->assertCount(2, $all);
    }

    /**
     * @return void
     */
    public function testCustomIteratorPassingGroup(): void
    {
        $all = [];
        \array_push($all, ...$this->manager->iterate(['grouped']));

        $this->assertInstanceOf(FooMetric::class, $all[0]);
        $this->assertInstanceOf(BarMetric::class, $all[1]);

        $this->assertCount(2, $all);
    }

    /**
     * @return void
     */
    public function testAllIteratorWithGroup(): void
    {
        $all = [];
        \array_push($all, ...$this->manager->iterateAll());

        $this->assertInstanceOf(FooMetric::class, $all[0]);
        $this->assertInstanceOf(BarMetric::class, $all[1]);
        $this->assertInstanceOf(FooMetric::class, $all[2]);
        $this->assertInstanceOf(BarMetric::class, $all[3]);

        $this->assertNotSame($all[0], $all[2]);
        $this->assertNotSame($all[1], $all[3]);

        $this->assertCount(4, $all);
    }

    /**
     * @return void
     */
    public function testClassesGetter(): void
    {
        $this->assertEmpty(
            \array_diff([FooMetric::class, BarMetric::class, FooGroup::class], $this->manager->classes())
        );
    }

    /**
     * @return void
     */
    public function testAliasesGetter(): void
    {
        $this->assertEmpty(\array_diff(['foo', 'grouped'], $this->manager->aliases()));
    }
}
