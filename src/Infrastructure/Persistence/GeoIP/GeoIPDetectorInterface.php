<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\GeoIP;

use _PHPStan_3e014c27f\Nette\Neon\Exception;
use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\SearchResponseParser;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\SpotifyApi\SearchRepository;
use App\Domain\SpotifyApi\TrackRepository;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use MaxMind\Db\Reader\InvalidDatabaseException;

interface GeoIPDetectorInterface
{
    public function detectCountry(string $fallbackIsoCode = 'US');
}
