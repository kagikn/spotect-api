<?php

namespace App\Exception\SpotifyApi;

class RateLimitExceededException extends SpotifyApiException
{
    public function __construct(
        string $endpoint,
        string $apiMessage,
        array|string $query = null,
        \Throwable $previous = null,
    ) {
        parent::__construct($endpoint, 429, $apiMessage, $query, $previous);
    }
}