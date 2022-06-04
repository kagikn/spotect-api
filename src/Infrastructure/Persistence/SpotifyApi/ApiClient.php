<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class ApiClient
{
    private GuzzleClient $client;

    public function __construct(GuzzleClient $customClient = null)
    {
        $this->client = $customClient
            ?? new GuzzleClient(['base_uri' => 'https://api.spotify.com/v1/']);
    }

    public function get(
        string $endpoint,
        string $accessToken,
        array|string $query = null,
        string $acceptLanguageHeader = null
    ): array|ErrorResponse {
        $configArray = $this->formatRequestConfigArray(
            $accessToken,
            $query,
            $acceptLanguageHeader
        );

        try {
            $response = $this->client->get($endpoint, $configArray);
        } catch (GuzzleException) {
            $errorMessage = 'Could not connect to Spotify API.';
            return new ErrorResponse(0, $errorMessage);
        }

        $statusCode = $response->getStatusCode();
        $responseJsonBodyStr = $response->getBody()->getContents();

        $parsedArray = json_decode($responseJsonBodyStr, true);
        if ($statusCode < 200 || $statusCode > 299) {
            // assume the error object always exists
            $error = $parsedArray['error'];
            return new ErrorResponse($statusCode, $error['message']);
        }

        return $parsedArray;
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
