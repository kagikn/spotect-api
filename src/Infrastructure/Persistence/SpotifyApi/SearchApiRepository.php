<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\SpotifyApi\SearchRepository;
use App\Exception\SpotifyApi\SpotifyApiException;
use GuzzleHttp\Exception\GuzzleException;

class SearchApiRepository implements SearchRepository
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    /**
     * @param  array  $queryParams
     * @param  string  $accessToken
     * @param ?string  $acceptLanguageHeader
     *
     * @return TrackPagingObject
     * @throws SpotifyApiException
     * @throws GuzzleException
     */
    public function searchForTrack(
        array $queryParams,
        string $accessToken,
        string $acceptLanguageHeader = null
    ): TrackPagingObject {
        $res = $this->client->get(
            'search',
            $accessToken,
            $queryParams,
            $acceptLanguageHeader
        );

        return TrackPagingObject::fromTrackSearchResponse(
            $res['tracks']
        );
    }
}
