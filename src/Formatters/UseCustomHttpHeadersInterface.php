<?php

namespace AvtoDev\AppMetrics\Formatters;

interface UseCustomHttpHeadersInterface
{
    /**
     * Current formatter provides custom HTTP response headers.
     *
     * @return string[]
     */
    public function httpHeaders(): array;
}
