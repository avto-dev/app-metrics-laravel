<?php

namespace AvtoDev\AppMetrics\Traits;

use Exception;
use Throwable;
use Illuminate\Contracts\Debug\ExceptionHandler;

trait WithThrowableReportingTrait
{
    /**
     * @var ExceptionHandler
     */
    protected $exception_handler;

    /**
     * @param Throwable $e
     */
    protected function reportThrowable(Throwable $e): void
    {
        if ($e instanceof Exception) {
            $this->exception_handler->report($e);
        } else {
            $this->exception_handler->report(new \RuntimeException($e->getMessage(), $e->getCode(), $e));
        }
    }
}
