<?php

namespace App\Domain\Entities\SpotifyApi;

class RestrictionsObject
{
    public function __construct(public readonly string $reason)
    {
    }
}
