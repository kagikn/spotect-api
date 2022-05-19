<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

use App\Domain\Entities\SpotifyApi\ErrorResponse;

interface ISpotifyAuthApi
{
    /**
     * @return SpotifyCredentials|ErrorResponse
     */
    public function getTokenClientCredentials(string $clientId, string $clientSecret): SpotifyCredentials|ErrorResponse;
}