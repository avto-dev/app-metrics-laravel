<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Http\Controllers;

use Illuminate\Support\Str;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;

/**
 * @covers \AvtoDev\AppMetrics\Http\Controllers\MetricsController<extended>
 * @covers \AvtoDev\AppMetrics\Http\Middleware\CheckMetricsSecretMiddleware<extended>
 */
class MetricsControllerFeatureTest extends AbstractUnitTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupMetricsConfig();

        $this->config->set('metrics.metric_classes', [
            'framework' => \AvtoDev\AppMetrics\Metrics\IlluminateFrameworkMetric::class,
            'host'      => \AvtoDev\AppMetrics\Metrics\HostInfoMetric::class,
        ]);
    }

    /**
     * @return void
     */
    public function testBasicControllerInvoking(): void
    {
        $response = $this
            ->get('/metrics')
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure(['*' => ['value']]);

        $this->assertCount(2, \json_decode($response->getContent(), true));
    }

    /**
     * @return void
     */
    public function testControllerInvokingPassingOnlyArgument(): void
    {
        $response = $this
            ->get('/metrics?only=host')
            ->assertSuccessful()
            ->assertJsonStructure(['*' => ['value']]);

        $this->assertCount(1, \json_decode($response->getContent(), true));
    }

    /**
     * @return void
     */
    public function testControllerInvokingPassingPrometheusFormat(): void
    {
        $this
            ->get('/metrics?format=prometheus')
            ->assertSuccessful()
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
