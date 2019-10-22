<?php

namespace AvtoDev\AppMetrics\Traits;

use Exception;
use Throwable;

trait ThrowableToExceptionTrait
{
    /**
     * @param Throwable $e
     *
     * @return Exception
     */
    protected function convertThrowableToException(Throwable $e): Exception
    {
        return $e instanceof Exception ? $e : new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    }
}
