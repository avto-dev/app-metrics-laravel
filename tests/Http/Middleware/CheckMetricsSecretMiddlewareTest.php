<?php

declare(strict_types = 1);

namespace AvtoDev\AppMetrics\Tests\Http\Middleware;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Http\Middleware\CheckMetricsSecretMiddleware;

/**
 * @covers \AvtoDev\AppMetrics\Http\Middleware\CheckMetricsSecretMiddleware<extended>
 */
class CheckMetricsSecretMiddlewareTest extends AbstractUnitTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupMetricsConfig();
    }

    /**
     * @return void
     */
    public function testSkipWhenSecretIsNotSet(): void
    {
        $this->config->set('metrics.http.secret', '');

        $this->assertSame(
            $expected = Str::random(),
            $this->middlewareFactory()->handle(Request::create('/foo', 'get'), static function () use ($expected) {
                return $expected;
            })
        );
    }

    /**
     * @return void
     */
    public function testSkipWhenSecretIsSetAndNothingPassed(): void
    {
        $this->config->set('metrics.http.secret', $secret = Str::random());

        $result = $this->middlewareFactory()->handle(Request::create('/foo', 'get'), static function (): void {
            //
        });

        /* @var JsonResponse $result */
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertJsonStringEqualsJsonString('{"error":true,"message":"Unauthorized"}', $result->getContent());
    }

    /**
     * @return void
     */
    public function testSkipWhenSecretIsSetAndParameterPassed(): void
    {
        $this->config->set('metrics.http.secret', $secret = Str::random());

        $this->assertSame(
            $expected = Str::random(),
            $this->middlewareFactory()
                ->handle(Request::create('/foo?secret=' . $secret, 'get'), static function () use ($expected) {
                    return $expected;
                })
        );
    }

    /**
     * @return void
     */
    public function testSkipWhenSecretIsSetAndHeaderPassed(): void
    {
        $this->config->set('metrics.http.secret', $secret = Str::random());

        $request = Request::create('/foo', 'get');
        $request->headers->set('X-SECRET', $secret);

        $this->assertSame(
            $expected = Str::random(),
            $this->middlewareFactory()->handle($request, static function () use ($expected) {
                return $expected;
            })
        );
    }

    protected function middlewareFactory(): CheckMetricsSecretMiddleware
    {
        return $this->app->make(CheckMetricsSecretMiddleware::class);
    }
}
