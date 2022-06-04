<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\SpotifyApi\AudioFeatureRepository;

class AudioFeatureApiRepository implements AudioFeatureRepository
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    /**
     * @param string $trackId
     * @param string $accessToken
     * @return AudioFeaturesObject|ErrorResponse
     */
    public function getAudioFeature(string $trackId, string $accessToken): AudioFeaturesObject|ErrorResponse
    {
        $res = $this->client->get('audio-features/' . $trackId, $accessToken);

        $jsonBody = $res->getBody()->getContents();
        $statusCode = $res->getStatusCode();

        if ($statusCode != 200) {
            $jsonArray = json_decode($jsonBody, true);
            $error = $jsonArray['error'];
            return new ErrorResponse($statusCode, $error['message']);
        }

        return AudioFeaturesObject::fromJson($jsonBody);
    }
}
