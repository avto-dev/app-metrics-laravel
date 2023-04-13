<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Tests\Formatters\Dictionaries;

use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Formatters\Dictionaries\PrometheusValuesDictionary;

/**
 * @covers \AvtoDev\AppMetrics\Formatters\Dictionaries\PrometheusValuesDictionary
 */
class PrometheusValuesDictionaryTest extends AbstractUnitTestCase
{
    /**
     * @return void
     */
    public function testConstants(): void
    {
        $expected_constants = [
            'NAN'          => 'Nan',
            'POSITIVE_INF' => '+Inf',
            'NEGATIVE_INF' => '-Inf',
        ];
        $constants = (new \ReflectionClass(PrometheusValuesDictionary::class))->getConstants();
        $this->assertEmpty(\array_diff($expected_constants, $constants));
        $this->assertEmpty(\array_diff(\array_keys($expected_constants), \array_keys($constants)));
    }

    /**
     * @return void
     */
    public function testAllMethod(): void
    {
        $constants = (new \ReflectionClass(PrometheusValuesDictionary::class))->getConstants();
        $this->assertEmpty(\array_diff($constants, PrometheusValuesDictionary::all()));
    }
}
