<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\GeoIP;

use App\Infrastructure\Persistence\GeoIP\GeoIPDetector;
use App\Infrastructure\Persistence\GeoIP\GeoIPDetectorInterface;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Tests\TestCase;

class GeoIPTest extends TestCase
{
    /**
     * @beforeClass
     */
    public static function setUpEnvVars(): void
    {
    }

    /**
     * @afterClass
     */
    public static function restoreEnvVars(): void
    {
    }

    /**
     * @backupGlobals
     */
    // Use a real database instead of mocked one since there are too many functions to mock in order to test with a
    // completely mocked database
    public function testDetectUsaIP()
    {
        $app = $this->getAppInstance();
        $container = $app->getContainer();
        $normalGeoIPDetector = $container->get(GeoIPDetectorInterface::class);

        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';
        $this->assertSame('US', $normalGeoIPDetector->detectCountry('JP'));

        $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
        $this->assertSame('JP', $normalGeoIPDetector->detectCountry('JP'));
    }

    public function testThrowExceptionWhenDatabaseIsInValid()
    {
        $readerStubThrowingException = $this->createStub(Reader::class);
        $readerStubThrowingException->method('country')
            ->willThrowException(new InvalidDatabaseException());

        $geoIpDetectorFailing = new GeoIPDetector($readerStubThrowingException);

        $this->expectException(InvalidDatabaseException::class);
        $geoIpDetectorFailing->detectCountry('JP');
    }
}
