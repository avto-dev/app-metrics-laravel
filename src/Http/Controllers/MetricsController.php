<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
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
     * @param Request         $request
     * @param ResponseFactory $response_factory
     *
     * @return Response
     */
    public function __invoke(Request $request,
                            ResponseFactory $response_factory): Response
    {
        $format = $request->get('format', 'json');

        //return $response_factory->json();
    }
}
