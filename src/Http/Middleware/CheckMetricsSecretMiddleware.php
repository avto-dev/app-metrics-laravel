<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use AvtoDev\AppMetrics\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class CheckMetricsSecretMiddleware
{
    /**
     * @var string|null
     */
    protected $secret;

    /**
     * @var ResponseFactory
     */
    protected $response_factory;

    /**
     * Create a new middleware instance.
     *
     * @param ConfigRepository $config
     * @param ResponseFactory  $response_factory
     */
    public function __construct(ConfigRepository $config, ResponseFactory $response_factory)
    {
        $this->secret           = $config->get(ServiceProvider::getConfigRootKeyName() . '.http.secret');
        $this->response_factory = $response_factory;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\is_string($this->secret) && \trim($this->secret) !== '') {
            if ($request->get('secret', $request->header('X-SECRET')) !== $this->secret) {
                return $this->response_factory->json([
                    'error'   => true,
                    'message' => 'Unauthorized',
                ], 401);
            }
        }

        return $next($request);
    }
}
