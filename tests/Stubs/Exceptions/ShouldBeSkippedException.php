<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Exceptions;

use RuntimeException;
use AvtoDev\AppMetrics\Exceptions\ShouldBeSkippedMetricExceptionInterface;

class ShouldBeSkippedException extends RuntimeException implements ShouldBeSkippedMetricExceptionInterface
{
    //
}
