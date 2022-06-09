<?php

declare(strict_types=1);

namespace Tests\FakeClasses\SpotifyApi;

use App\Domain\SpotifyCredentials\ISpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class FakeAuthApi implements ISpotifyAuthApi
{
    public function __construct()
    {
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
        if (empty($clientId) || empty($clientSecret)) {
            throw new ClientException(
                'Fake error',
                new Request('POST', 'token'),
                new Response(400, '', 'invalid parameter')
            );
        }

        return new SpotifyGenericCredentials(
            '',
            0,
        );
    }
}
