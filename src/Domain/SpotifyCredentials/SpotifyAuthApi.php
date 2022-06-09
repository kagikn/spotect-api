<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class SpotifyAuthApi implements ISpotifyAuthApi
{
    private GuzzleClient $guzzleClient;

    public function __construct(GuzzleClient $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient
            ?? new GuzzleClient(
                ['base_uri' => 'https://accounts.spotify.com/api/token/']
            );
    }

    /**
     * @param  string  $clientId
     * @param  string  $clientSecret
     *
     * @return SpotifyGenericCredentials
     */
    public function getTokenClientCredentials(
        string $clientId,
        string $clientSecret
    ): SpotifyGenericCredentials {
        $res = $this->tryFetchToken($clientId, $clientSecret);
        $json = json_decode($res->getBody()->getContents(), true);
        return new SpotifyGenericCredentials(
            $json['access_token'],
            $json['expires_in'] + time()
        );
    }

    private function tryFetchToken(
        string $clientId,
        string $clientSecret
    ): ResponseInterface {
        $authStr = 'Basic ' . base64_encode($clientId . ':' . $clientSecret);
        return $this->guzzleClient->post('', [
            'headers' => [
                'Authorization' => $authStr
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ],
        ]);
    }
}
