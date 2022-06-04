<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class SpotifyAuthApi implements ISpotifyAuthApi
{
    private GuzzleClient $guzzleClient;

    public function __construct(GuzzleClient $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient
            ?? new GuzzleClient(['base_uri' => 'https://accounts.spotify.com/api/token/']);
    }

    private function tryFetchToken(string $clientId, string $clientSecret): ?ResponseInterface
    {
        try {
            return $this->guzzleClient->post('', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ],
                'http_errors' => false,
            ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $ex) {
            return null;
        }
    }

    /**
     * @return SpotifyGenericCredentials|ErrorResponse
     */
    public function getTokenClientCredentials(
        string $clientId,
        string $clientSecret
    ): SpotifyGenericCredentials|ErrorResponse
    {
        $res = $this->tryFetchToken($clientId, $clientSecret);

        $json = json_decode($res->getBody()->getContents(), true);

        if ($res->getStatusCode() != 200) {
            $error = $json['error'];
            $errorMsg = is_string($error) ? $error : 'server_error';

            return new ErrorResponse(500, $errorMsg);
        }

        return new SpotifyGenericCredentials(
            $json['access_token'],
            $json['expires_in'] + time()
        );
    }
}