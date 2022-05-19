<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Domain\SpotifyApi\AudioFeatureRepository;
use GuzzleHttp\Client as GuzzleClient;

class ApiClient
{
    private GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient(['base_uri' => 'https://api.spotify.com/v1/']);
    }

    public function get(
        string       $endpoint,
        string       $accessToken,
        array|string $query = null,
        string       $acceptLanguageHeader = null
    )
    {
        $configArray = $this->formatRequestConfigArray($accessToken, $query, $acceptLanguageHeader);
        return $this->client->get($endpoint, $configArray);
    }

    private function formatRequestConfigArray(
        string       $accessToken,
        array|string $query = null,
        string       $acceptLanguageHeader = null
    )
    {
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ];

        if (isset($acceptLanguageHeader)) {
            $headers += ['Accept-Language' => $acceptLanguageHeader];
        }

        $configArray = [
            'headers' => $headers,
            'http_errors' => false,
        ];

        if (isset($query)) {
            $configArray += ['query' => $query];
        }

        return $configArray;
    }
}