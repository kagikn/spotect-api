<?php

namespace App\Exception\SpotifyApi;

class BadRequestParameterException extends SpotifyApiException
{
    public function __construct(
        string $endpoint,
        string $apiMessage,
        array|string $query = null,
        \Throwable $previous = null,
    ) {
        parent::__construct($endpoint, 400, $apiMessage, $query, $previous);
    }
}
