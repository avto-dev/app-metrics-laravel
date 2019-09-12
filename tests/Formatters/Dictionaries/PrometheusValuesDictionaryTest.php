<?php

declare(strict_types=1);

namespace AvtoDev\AppMetrics\Tests\Formatters\Dictionaries;

use AvtoDev\AppMetrics\Tests\AbstractUnitTestCase;
use AvtoDev\AppMetrics\Formatters\Dictionaries\PrometheusValuesDictionary;

/**
 * Class PrometheusValuesDictionaryTest.
 *
 * @covers \AvtoDev\AppMetrics\Formatters\Dictionaries\PrometheusValuesDictionary<extended>
 */
class PrometheusValuesDictionaryTest extends AbstractUnitTestCase
{
    /**
     * @return void
     */
    public function testConstants(): void
    {
        $constants = (new \ReflectionClass(PrometheusValuesDictionary::class))->getConstants();
        $this->assertEmpty(\array_diff($this->getExpectedEntries(), $constants));
        $this->assertEmpty(\array_diff(\array_keys($this->getExpectedEntries()), \array_keys($constants)));
    }

    /**
     * @return void
     */
    public function testAllMethod(): void
    {
        $this->assertEmpty(\array_diff($this->getExpectedEntries(), PrometheusValuesDictionary::all()));
    }

    /**
     * @return array
     */
    protected function getExpectedEntries():array
    {
        return  [
            'NAN'          => 'Nan',
            'POSITIVE_INF' => '+Inf',
            'NEGATIVE_INF' => '-Inf',
        ];
    }
}
