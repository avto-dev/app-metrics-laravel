<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use AvtoDev\AppMetrics\MetricsManager;
use AvtoDev\AppMetrics\ServiceProvider;
use AvtoDev\AppMetrics\FormattersManager;
use AvtoDev\AppMetrics\MetricsManagerInterface;
use AvtoDev\AppMetrics\FormattersManagerInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * @covers \AvtoDev\AppMetrics\ServiceProvider<extended>
 */
class ServiceProviderTest extends AbstractUnitTestCase
{
    /**
     * @return void
     */
    public function testGetConfigRootKeyName(): void
    {
        $this->assertSame('metrics', ServiceProvider::getConfigRootKeyName());
    }

    /**
     * @return void
     */
    public function testGetConfigPath(): void
    {
        $this->assertSame(\realpath(__DIR__ . '/../config/metrics.php'), \realpath(ServiceProvider::getConfigPath()));
    }

    /**
     * @return void
     */
    public function testServicesRegistration(): void
    {
        /* @var MetricsManager $metrics_manager */
        $this->assertInstanceOf(
            MetricsManager::class,
            $metrics_manager = $this->app->make(MetricsManagerInterface::class)
        );
        $this->assertEquals($this->config->get('metrics.metric_classes'), \array_values($metrics_manager->classes()));

        /* @var FormattersManager $formatters_manager */
        $this->assertInstanceOf(
            FormattersManager::class,
            $formatters_manager = $this->app->make(FormattersManagerInterface::class)
        );
        $this->assertEquals(\array_keys($this->config->get('metrics.formatters')), $formatters_manager->aliases());
    }

    /**
     * @return void
     */
    public function testRouteBooting(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        /** @var Route $route */
        $route = $router->getRoutes()->getByName($this->config->get('metrics.http.name'));

        $this->assertSame(\ltrim($this->config->get('metrics.http.uri'), '/'), $route->uri());
        $this->assertSame($this->config->get('metrics.http.controller'), $route->getAction()['controller']);
    }

    /**
     * @return void
     */
    public function testRoutesNotBootedWhenDisabled(): void
    {
        $this->app->flush();
        unset($this->app);

        $this->app = $this->createApplication([]);
        $this->app->make(ConfigRepository::class)->set('metrics.http.enabled', false);

        $this->app->register(ServiceProvider::class);

        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $this->assertNull($router->getRoutes()->getByName($this->config->get('metrics.http.name')));
    }
}
