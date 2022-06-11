<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

use GuzzleHttp\Exception\GuzzleException;

interface ISpotifyAuthApi
{
    /**
     * @param  string  $clientId
     * @param  string  $clientSecret
     *
     * @return SpotifyCredentials
     * @throws GuzzleException
     */
    public function getTokenClientCredentials(
        string $clientId,
        string $clientSecret
    ): SpotifyCredentials;
}
