<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Exception\SpotifyApi\BadOAuthRequestException;
use App\Exception\SpotifyApi\BadRequestParameterException;
use App\Exception\SpotifyApi\InvalidTokenException;
use App\Exception\SpotifyApi\RateLimitExceededException;
use App\Exception\SpotifyApi\SpotifyApiException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Slim\Exception\HttpException;

class ApiClient
{
    private GuzzleClient $client;

    public function __construct(GuzzleClient $customClient = null)
    {
        $this->client = $customClient
            ?? new GuzzleClient(['base_uri' => 'https://api.spotify.com/v1/']);
    }

    /**
     * @param  string  $endpoint
     * @param  string  $accessToken
     * @param  array|string|null  $query
     * @param  string|null  $acceptLanguageHeader
     *
     * @return array
     * @throws SpotifyApiException
     * @throws GuzzleException
     */
    public function get(
        string $endpoint,
        string $accessToken,
        array|string $query = null,
        string $acceptLanguageHeader = null
    ): array {
        $configArray = $this->formatRequestConfigArray(
            $accessToken,
            $query,
            $acceptLanguageHeader
        );
        try {
            $response = $this->client->get($endpoint, $configArray);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $jsonBody = json_decode($responseBody, true);
            $apiMessage = $jsonBody['error']['message'];

            throw match ($e->getCode()) {
                400 => new BadRequestParameterException(
                    $endpoint,
                    $apiMessage,
                    $query
                ),
                401 => new InvalidTokenException(
                    $endpoint,
                    $apiMessage,
                    $query
                ),
                403 => new BadOAuthRequestException(
                    $endpoint,
                    $apiMessage,
                    $query
                ),
                429 => new RateLimitExceededException(
                    $endpoint,
                    $apiMessage,
                    $query
                ),
                default => $e,
            };
        }

        $responseJsonBodyStr = $response->getBody()->getContents();
        return json_decode($responseJsonBodyStr, true);
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
        ];

        if (isset($query)) {
            $configArray += ['query' => $query];
        }

        return $configArray;
    }
}
