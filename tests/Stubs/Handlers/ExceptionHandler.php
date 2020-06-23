<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Tests\Stubs\Handlers;

use Throwable;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler implements \Illuminate\Contracts\Debug\ExceptionHandler
{
    /**
     * Methods calls counters.
     *
     * @var array
     */
    protected $calls = [
        'report'           => 0,
        'render'           => 0,
        'renderForConsole' => 0,
    ];

    /**
     * Received exceptions array.
     *
     * @var \Exception[]
     */
    protected $exceptions = [];

    /**
     * {@inheritdoc}
     */
    public function report($e): void
    {
        $this->calls['report']++;
        $this->exceptions[] = $e;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldReport($e): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function render($request, $e): Response
    {
        $this->calls['render']++;
        $this->exceptions[] = $e;

        return new Response($e->getMessage(), $e->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, $e): void
    {
        $this->calls['renderForConsole']++;
        $this->exceptions[] = $e;
    }

    /**
     * Return calls count for passed method.
     *
     * @param string $method_name
     *
     * @return int
     */
    public function getCallsCount(string $method_name): int
    {
        return $this->calls[$method_name] ?? 0;
    }

    /**
     * Checks if exception with passed class name and message was handled.
     *
     * @param string $exception_class_name
     * @param string $exception_message
     *
     * @return bool
     */
    public function hasException(string $exception_class_name, string $exception_message): bool
    {
        foreach ($this->exceptions as $exception) {
            if ($exception instanceof $exception_class_name && $exception->getMessage() === $exception_message) {
                return true;
            }
        }

        return false;
    }
}
