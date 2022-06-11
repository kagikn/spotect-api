<?php

declare(strict_types=1);

namespace Tests\FakeClasses;

use App\Infrastructure\Persistence\GeoIP\GeoIPDetectorInterface;

class FakeGeoIPDetector implements GeoIPDetectorInterface
{
    public function __construct()
    {
    }

    /**
     * @param  string  $fallbackIsoCode
     *
     * @return string
     */
    public function detectCountry(string $fallbackIsoCode = 'US'): string
    {
        return $fallbackIsoCode;
    }
}
