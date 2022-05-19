<?php

declare(strict_types=1);

namespace App\Domain\SpotifyApi;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;

interface SearchRepository
{
    /**
     * @param array $queryParams
     * @param string $accessToken
     * @param ?string $acceptLanguageHeader
     * @return TrackPagingObject|ErrorResponse
     */
    public function searchForTrack(
        array  $queryParams,
        string $accessToken,
        string $acceptLanguageHeader = null
    ): TrackPagingObject|ErrorResponse;
}
