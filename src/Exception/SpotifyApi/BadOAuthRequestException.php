<?php

namespace App\Exception\SpotifyApi;

class BadOAuthRequestException extends SpotifyApiException
{
    public function __construct(
        string $endpoint,
        string $apiMessage,
        array|string $query = null,
        \Throwable $previous = null,
    ) {
        parent::__construct($endpoint, 403, $apiMessage, $query, $previous);
    }
}