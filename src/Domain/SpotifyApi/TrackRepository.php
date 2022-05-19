<?php

declare(strict_types=1);

namespace App\Domain\SpotifyApi;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;

interface TrackRepository
{
    /**
     * @param string $trackId
     * @param string $accessToken
     * @param ?string $market
     * @param ?string $acceptLanguageHeader
     * @return TrackObjectFullEntity|ErrorResponse
     */
    public function getTrackInfo(
        string $trackId,
        string $accessToken,
        string $market = null,
        string $acceptLanguageHeader = null
    ): TrackObjectFullEntity|ErrorResponse;
}
