<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Exception\SpotifyApi\SpotifyApiException;
use App\Domain\SpotifyApi\AudioFeatureRepository;
use GuzzleHttp\Exception\GuzzleException;

class AudioFeatureApiRepository implements AudioFeatureRepository
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

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
    ): AudioFeaturesObject {
        $res = $this->client->get('audio-features/' . $trackId, $accessToken);
        return AudioFeaturesObject::fromItemArray($res);
    }
}
