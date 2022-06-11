<?php

declare(strict_types=1);

namespace App\Domain\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Exception\SpotifyApi\SpotifyApiException;
use GuzzleHttp\Exception\GuzzleException;

interface AudioFeatureRepository
{
    /**
     * @param  string  $trackId
     * @param  string  $accessToken
     *
     * @return AudioFeaturesObject
     * @throws SpotifyApiException
     * @throws GuzzleException
     */
    public function getAudioFeature(
        string $trackId,
        string $accessToken
    ): AudioFeaturesObject;
}
