<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Container\Container;
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
            $router->get($config->get("{$root}.http.route"), [
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
        $this->app->bind(MetricsManagerInterface::class, static function (Container $container) {
            /** @var ConfigRepository $config */
            $config = $container->make(ConfigRepository::class);

            return new MetricsManager(
                $container,
                (array) $config->get(static::getConfigRootKeyName() . '.metrics')
            );
        });
    }

    /**
     * @return void
     */
    protected function registerFormattersManager(): void
    {
        $this->app->bind(FormattersManagerInterface::class, static function (Container $container) {
            /** @var ConfigRepository $config */
            $config = $container->make(ConfigRepository::class);
            $root   = static::getConfigRootKeyName();

            return new FormattersManager(
                $container,
                (array) $config->get("{$root}.formatters"),
                (string) $config->get("{$root}.default_format")
            );
        });
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
