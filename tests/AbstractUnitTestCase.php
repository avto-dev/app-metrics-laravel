<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests;

use AvtoDev\AppMetrics\ServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\FooMetric;
use AvtoDev\AppMetrics\Tests\Stubs\Metrics\BarMetric;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

abstract class AbstractUnitTestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * @var ConfigRepository
     */
    protected $config;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->app->make(ConfigRepository::class);
    }

    protected function setupMetricsConfig(): void
    {
        $this->config->set('app.debug', true);
        $this->config->set('metrics.metric_classes', [
            'foo' => FooMetric::class,
            'bar' => BarMetric::class,
        ]);
        $this->config->set('metrics.default_format', 'json');
        $this->config->set('metrics.http.secret', '');
    }

    /**
     * Creates the application.
     *
     * @param string[] $providers
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(array $providers = [ServiceProvider::class])
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        foreach ($providers as $provider) {
            $app->register($provider);
        }

        return $app;
    }
}
