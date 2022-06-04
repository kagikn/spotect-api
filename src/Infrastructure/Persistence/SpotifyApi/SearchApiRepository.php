<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\SpotifyApi\SearchRepository;

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
     * @return TrackPagingObject|ErrorResponse
     */
    public function searchForTrack(
        array $queryParams,
        string $accessToken,
        string $acceptLanguageHeader = null
    ): TrackPagingObject|ErrorResponse {
        $res = $this->client->get(
            'search',
            $accessToken,
            $queryParams,
            $acceptLanguageHeader
        );

        if ($res instanceof ErrorResponse) {
            return $res;
        }

        return TrackPagingObject::fromTrackSearchResponse(
            $res['tracks']
        );
    }
}
