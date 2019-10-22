<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Tests\Traits;

use LogicException;
use RuntimeException;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Traits\ThrowableToExceptionTrait;
use AvtoDev\AppMetrics\Tests\Stubs\Exceptions\ShouldBeSkippedException;

class ThrowableToExceptionTraitTest extends AbstractUnitTestCase
{
    use ThrowableToExceptionTrait;

    /**
     * @return void;
     */
    public function testReportThrowable(): void
    {
        $exception = new LogicException('Test logic exception');
        $converted = $this->convertThrowableToException($exception);
        $this->assertSame($exception, $converted);

        $exception = new ShouldBeSkippedException('Test skipping exception');
        $converted = $this->convertThrowableToException($exception);
        $this->assertSame($exception, $converted);

        $exception = new \Error('Test error');
        $converted = $this->convertThrowableToException($exception);
        $this->assertInstanceOf(RuntimeException::class, $converted);
        $this->assertSame($exception->getMessage(), $converted->getMessage());
        $this->assertSame($exception->getCode(), $converted->getCode());
        $this->assertSame($exception, $converted->getPrevious());
    }
}
