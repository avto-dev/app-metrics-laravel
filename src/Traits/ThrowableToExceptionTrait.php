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
        if ($e instanceof Exception) {
            return $e;
        }

        return new \RuntimeException($e->getMessage(), $e->getCode(), $e);
    }
}
