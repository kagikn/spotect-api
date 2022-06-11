<?php

declare(strict_types=1);

namespace App\Domain\SpotifyApi;

use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Exception\SpotifyApi\SpotifyApiException;
use GuzzleHttp\Exception\GuzzleException;

interface SearchRepository
{
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
    ): TrackPagingObject;
}
