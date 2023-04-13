<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests;

use stdClass;
use Illuminate\Support\Str;
use InvalidArgumentException;
use AvtoDev\AppMetrics\FormattersManager;
use AvtoDev\AppMetrics\FormattersManagerInterface;
use AvtoDev\AppMetrics\Tests\Stubs\Formatters\BarFormatter;
use AvtoDev\AppMetrics\Tests\Stubs\Formatters\FooFormatter;

/**
 * @covers \AvtoDev\AppMetrics\FormattersManager
 */
class FormattersManagerTest extends AbstractUnitTestCase
{
    /**
     * @var FormattersManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new FormattersManager($this->app, [
            'foo' => FooFormatter::class,
            'bar' => BarFormatter::class,
        ], 'bar');
    }

    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(FormattersManagerInterface::class, $this->manager);
    }

    /**
     * @return void
     */
    public function testMake(): void
    {
        $this->assertInstanceOf(FooFormatter::class, $this->manager->make('foo'));
        $this->assertInstanceOf(BarFormatter::class, $this->manager->make('bar'));
        $this->assertInstanceOf(BarFormatter::class, $this->manager->default());
    }

    /**
     * @return void
     */
    public function testMakeDefaultFormatterThrowsException(): void
    {
        $manager = new FormattersManager($this->app, [
            'foo' => FooFormatter::class,
            'bar' => BarFormatter::class,
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Default formatter was not set');

        $manager->default();
    }

    /**
     * @return void
     */
    public function testAddFactory(): void
    {
        $this->manager = new FormattersManager($this->app, []);

        $this->assertFalse($this->manager->exists('foo'));

        $this->manager->addFactory($alias = Str::random(), FooFormatter::class);

        $this->assertInstanceOf(FooFormatter::class, $this->manager->make($alias));
    }

    /**
     * @return void
     */
    public function testAddFactoryUsingNotExistingClassName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('~class.*not.*exists~i');

        $this->manager->addFactory(Str::random(), Str::random());
    }

    /**
     * @return void
     */
    public function testAddFactoryUsingWrongClassName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('~class.*must.*implements~i');

        $this->manager->addFactory(Str::random(), \stdClass::class);
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
        $this->assertTrue($this->manager->exists('foo'));
        $this->assertTrue($this->manager->exists('bar'));

        $this->assertFalse($this->manager->exists(Str::random()));
        $this->assertFalse($this->manager->exists(stdClass::class));
    }

    /**
     * @return void
     */
    public function testAliasesGetter(): void
    {
        $this->assertEmpty(\array_diff(['foo', 'bar'], $this->manager->aliases()));
    }
}
