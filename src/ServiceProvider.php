<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Get config root key name.
     *
     * @return string
     */
    public static function getConfigRootKeyName(): string
    {
        return \basename(static::getConfigPath(), '.php');
    }

    /**
     * Returns path to the configuration file.
     *
     * @return string
     */
    public static function getConfigPath(): string
    {
        return __DIR__ . '/../config/metrics.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->initializeConfigs();
        $this->registerMetricsManager();
        $this->registerFormattersManager();
    }

    /**
     * @param ConfigRepository $config
     * @param Router           $router
     *
     * @return void
     */
    public function boot(ConfigRepository $config, Router $router): void
    {
        $root = static::getConfigRootKeyName();

        if ($config->get("{$root}.http.enabled") === true) {
            /** @var string $uri */
            $uri = $config->get("{$root}.http.uri", '');

            $router->get($uri, [
                'uses' => $config->get("{$root}.http.controller"),
                'as'   => $config->get("{$root}.http.name"),
            ]);
        }
    }

    /**
     * @return void
     */
    protected function registerMetricsManager(): void
    {
        $this->app->bind(
            MetricsManagerInterface::class,
            static function (Container $container): MetricsManagerInterface {
                /** @var ConfigRepository $config */
                $config = $container->make(ConfigRepository::class);
                /** @var ExceptionHandler $exception_handler */
                $exception_handler = $container->make(ExceptionHandler::class);
                /** @var string[] $metrics */
                $metrics = $config->get(static::getConfigRootKeyName() . '.metric_classes');

                return new MetricsManager(
                    $container,
                    $metrics,
                    $exception_handler
                );
            }
        );
    }

    /**
     * @return void
     */
    protected function registerFormattersManager(): void
    {
        $this->app->bind(
            FormattersManagerInterface::class,
            static function (Container $container): FormattersManagerInterface {
                /** @var ConfigRepository $config */
                $config = $container->make(ConfigRepository::class);
                $root   = static::getConfigRootKeyName();
                /** @var string[] $formatters */
                $formatters = $config->get("{$root}.formatters", []);
                /** @var string $default_format */
                $default_format = $config->get("{$root}.default_format", "");

                return new FormattersManager(
                    $container,
                    $formatters,
                    $default_format
                );
            }
        );
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs(): void
    {
        $this->mergeConfigFrom(static::getConfigPath(), static::getConfigRootKeyName());

        $this->publishes([
            \realpath(static::getConfigPath()) => config_path(\basename(static::getConfigPath())),
        ], 'config');
    }
}
