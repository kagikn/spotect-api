<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

interface SpotifyCredentialsRepository
{
    public function store(SpotifyCredentials $credentials): bool;

    public function get(): ?SpotifyCredentials;
}
