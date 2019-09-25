<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Http\Controllers;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AvtoDev\AppMetrics\Metrics\MetricInterface;
use AvtoDev\AppMetrics\MetricsManagerInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use AvtoDev\AppMetrics\FormattersManagerInterface;
use AvtoDev\AppMetrics\Metrics\MetricsGroupInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use AvtoDev\AppMetrics\Formatters\UseCustomHttpHeadersInterface;
use AvtoDev\AppMetrics\Http\Middleware\CheckMetricsSecretMiddleware;

class MetricsController extends \Illuminate\Routing\Controller
{
    /**
     * Create a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware(CheckMetricsSecretMiddleware::class);
    }

    /**
     * @param Request                    $request
     * @param MetricsManagerInterface    $metrics_manager
     * @param FormattersManagerInterface $formatters_manager
     * @param ExceptionHandler           $exception_handler
     * @param ConfigRepository           $config
     * @param ResponseFactory            $response_factory
     *
     * @return Response
     */
    public function __invoke(Request $request,
                             MetricsManagerInterface $metrics_manager,
                             FormattersManagerInterface $formatters_manager,
                             ExceptionHandler $exception_handler,
                             ConfigRepository $config,
                             ResponseFactory $response_factory): Response
    {
        try {
            $format = $request->get('format');
            $only   = \array_filter(\explode(',', $request->get('only', '')));

            $formatter = \is_string($format) && $format !== ''
                ? $formatters_manager->make($format)
                : $formatters_manager->default();

            $metrics = empty($only)
                ? $metrics_manager->iterateAll()
                : $metrics_manager->iterate($only);

            $headers = $formatter instanceof UseCustomHttpHeadersInterface
                ? $formatter->httpHeaders()
                : [];

            return $response_factory->make((string) $formatter->format($metrics), 200, $headers);
        } catch (Exception $e) {
            $exception_handler->report($e);

            return $response_factory->json([
                'error'   => true,
                'message' => $e->getMessage(),
                'trace'   => $config->get('app.debug', false) === true
                    ? $e->getTraceAsString()
                    : null,
            ], 500);
        }
    }
}
