<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient(
            ['base_uri' => 'https://api.spotify.com/v1/']
        );
    }

    public function get(
        string $endpoint,
        string $accessToken,
        array|string $query = null,
        string $acceptLanguageHeader = null
    ): ResponseInterface {
        $configArray = $this->formatRequestConfigArray(
            $accessToken,
            $query,
            $acceptLanguageHeader
        );
        return $this->client->get($endpoint, $configArray);
    }

    private function formatRequestConfigArray(
        string $accessToken,
        array|string $query = null,
        string $acceptLanguageHeader = null
    ): array {
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
