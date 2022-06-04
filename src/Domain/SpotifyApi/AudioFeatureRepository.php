<?php

declare(strict_types=1);

namespace App\Domain\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Domain\Entities\SpotifyApi\ErrorResponse;

interface AudioFeatureRepository
{
    /**
     * @param  string  $trackId
     * @param  string  $accessToken
     *
     * @return AudioFeaturesObject|ErrorResponse
     */
    public function getAudioFeature(
        string $trackId,
        string $accessToken
    ): AudioFeaturesObject|ErrorResponse;
}
