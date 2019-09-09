<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use AvtoDev\AppMetrics\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class CheckMetricsSecretMiddleware
{
    /**
     * @var string|null
     */
    protected $secret;

    /**
     * Create a new middleware instance.
     *
     * @param ConfigRepository $config
     */
    public function __construct(ConfigRepository $config)
    {
        $this->secret = $config->get(ServiceProvider::getConfigRootKeyName() . '.http.secret');
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @throws HttpException
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\is_string($this->secret) && $this->secret !== '') {
            // @todo: write code
        }

        return $next($request);
    }
}
