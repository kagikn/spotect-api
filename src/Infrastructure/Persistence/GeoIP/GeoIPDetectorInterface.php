<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\GeoIP;

interface GeoIPDetectorInterface
{
    public function detectCountry(string $fallbackIsoCode = 'US');
}
