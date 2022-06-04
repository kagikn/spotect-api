<?php

declare(strict_types=1);

namespace App\Domain\SpotifyCredentials;

class SpotifyGenericCredentials implements SpotifyCredentials
{
    private string $accessToken;
    private int $accessTokenExpirationTimestamp;

    public function __construct(string $accessToken, int $accessTokenExpirationTimestamp)
    {
        $this->accessToken = $accessToken;
        $this->accessTokenExpirationTimestamp = $accessTokenExpirationTimestamp;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return null;
    }

    /**
     * @return string[]
     */
    public function getScope(): array
    {
        return [];
    }

    /**
     * @return int
     */
    public function getAccessTokenExpirationTimestamp(): int
    {
        return $this->accessTokenExpirationTimestamp;
    }
}
