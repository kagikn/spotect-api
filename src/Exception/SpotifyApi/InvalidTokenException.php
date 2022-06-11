<?php

namespace App\Exception\SpotifyApi;

class InvalidTokenException extends SpotifyApiException
{
    public function __construct(
        string $endpoint,
        string $apiMessage,
        array|string $query = null,
        \Throwable $previous = null,
    ) {
        parent::__construct($endpoint, 401, $apiMessage, $query, $previous);
    }
}