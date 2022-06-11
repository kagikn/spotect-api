<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\SpotifyApi\TrackRepository;
use App\Exception\SpotifyApi\SpotifyApiException;
use GuzzleHttp\Exception\GuzzleException;

class TrackApiRepository implements TrackRepository
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    /**
     * @param  string  $trackId
     * @param  string  $accessToken
     * @param ?string  $market
     * @param ?string  $acceptLanguageHeader
     *
     * @return TrackObjectFullEntity
     * @throws SpotifyApiException
     * @throws GuzzleException
     */
    public function getTrackInfo(
        string $trackId,
        string $accessToken,
        string $market = null,
        string $acceptLanguageHeader = null
    ): TrackObjectFullEntity {
        $res = $this->client->get(
            'tracks/' . $trackId,
            $accessToken,
            $market,
            $acceptLanguageHeader,
        );
        return TrackObjectFullEntity::fromTrackObjItemArray($res);
    }
}
