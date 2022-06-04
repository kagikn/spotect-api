<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\GeoIP;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoIPDetector implements GeoIPDetectorInterface
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $fallbackIsoCode
     * @return string
     * @throws InvalidDatabaseException
     */
    public function detectCountry(string $fallbackIsoCode = 'US'): string
    {
        try {
            $geoIPReader = $this->reader;
            return $geoIPReader->country($this->getIPAddress())->country->isoCode; // can't be null
        } catch (AddressNotFoundException) {
            return $fallbackIsoCode;
        }
    }

    private function getIPAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}
