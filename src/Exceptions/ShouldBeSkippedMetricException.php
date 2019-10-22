<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Exceptions;

use RuntimeException;

final class ShouldBeSkippedMetricException extends RuntimeException implements ShouldBeSkippedMetricExceptionInterface
{

}
