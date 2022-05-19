<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

interface SpotifyCredentials
{
    /**
     * @return string
     */
    public function getAccessToken(): string;

    public function getRefreshToken(): ?string;

    /**
     * @return string[]
     */
    public function getScope(): array;

    /**
     * @return int
     */
    public function getAccessTokenExpirationTimestamp(): int;
}
