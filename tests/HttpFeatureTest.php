<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests;

use Illuminate\Support\Str;

/**
 * @covers \AvtoDev\AppMetrics\Http\Controllers\MetricsController<extended>
 * @covers \AvtoDev\AppMetrics\Http\Middleware\CheckMetricsSecretMiddleware<extended>
 */
class HttpFeatureTest extends AbstractUnitTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config->set('metrics.metric_classes', [
            'framework' => \AvtoDev\AppMetrics\Metrics\IlluminateFrameworkMetric::class,
            'host'      => \AvtoDev\AppMetrics\Metrics\HostInfoMetric::class,
        ]);
        $this->config->set('metrics.default_format', 'json');
        $this->config->set('metrics.http.secret', '');
    }

    /**
     * @return void
     */
    public function testBasicControllerInvoking(): void
    {
        $this
            ->get('/metrics')
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonCount(2)
            ->assertJsonStructure(['*' => ['value']]);
    }

    /**
     * @return void
     */
    public function testControllerInvokingPassingOnlyArgument(): void
    {
        $this
            ->get('/metrics?only=host')
            ->assertSuccessful()
            ->assertJsonCount(1)
            ->assertJsonStructure(['*' => ['value']]);
    }

    /**
     * @return void
     */
    public function testControllerInvokingPassingPrometheusFormat(): void
    {
        $this
            ->get('/metrics?format=prometheus')
            ->assertSuccessful()
            ->dump()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('illuminate')
            ->assertSee('host_info');
    }

    /**
     * @return void
     */
    public function testControllerInvokingUsingInvalidFormat(): void
    {
        $this
            ->get('/metrics?format=' . Str::random())
            ->assertStatus(500)
            ->assertJsonStructure(['error', 'message']);
    }

    /**
     * @return void
     */
    public function testControllerInvokingUsingInvalidMetricAlias(): void
    {
        $this
            ->get('/metrics?only=' . Str::random())
            ->assertStatus(500)
            ->assertJsonStructure(['error', 'message']);
    }

    /**
     * @return void
     */
    public function testControllerWithSecretSet(): void
    {
        $this->config->set('metrics.http.secret', $secret = Str::random());

        $this
            ->get('/metrics')
            ->assertStatus(401)
            ->assertJsonStructure(['error', 'message']);

        $this
            ->get('/metrics?secret=' . $secret)
            ->assertSuccessful()
            ->assertJsonStructure(['*' => ['value']]);

        $this
            ->get('/metrics', ['X-SECRET' => $secret])
            ->assertSuccessful()
            ->assertJsonStructure(['*' => ['value']]);
    }
}
