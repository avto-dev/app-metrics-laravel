<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Tests\Traits;

use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Tests\Stubs\Exceptions\ShouldBeSkippedException;
use AvtoDev\AppMetrics\Traits\WithThrowableReportingTrait;
use AvtoDev\AppMetrics\Tests\Stubs\Handlers\ExceptionHandler as ExceptionHandlerStub;
use LogicException;
use RuntimeException;

class WithThrowableReportingTraitTest extends AbstractUnitTestCase
{
    use WithThrowableReportingTrait;

    /**
     * @return void;
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->exception_handler = new ExceptionHandlerStub();
    }

    /**
     * @return void;
     */
    public function testReportThrowable(): void
    {
        $exception = new LogicException('Test exception');
        $this->reportThrowable($exception);

        $this->assertSame(1, $this->exception_handler->getCallsCount('report'));
        $this->assertTrue(
            $this->exception_handler->hasException(LogicException::class, 'Test exception')
        );

        $exception = new ShouldBeSkippedException('Test exception');
        $this->reportThrowable($exception);

        $this->assertSame(2, $this->exception_handler->getCallsCount('report'));
        $this->assertTrue(
            $this->exception_handler->hasException(ShouldBeSkippedException::class, 'Test exception')
        );

        $exception = new \Error('Test error');
        $this->reportThrowable($exception);

        $this->assertSame(3, $this->exception_handler->getCallsCount('report'));
        $this->assertTrue(
            // Error should be transformed to RuntimeException
            $this->exception_handler->hasException(RuntimeException::class, 'Test error')
        );
    }
}
