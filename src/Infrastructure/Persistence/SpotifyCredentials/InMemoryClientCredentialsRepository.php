<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyCredentials;

use App\Domain\SpotifyCredentials\SpotifyCredentials;
use App\Domain\SpotifyCredentials\SpotifyCredentialsRepository;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;

class InMemoryClientCredentialsRepository implements SpotifyCredentialsRepository
{
    public ?string $tokenDataEncrypted;

    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function store(SpotifyCredentials $credentials): bool
    {
        $accessToken = $credentials->getAccessToken();
        $accessTokenExpirationTimestamp = $credentials->getAccessTokenExpirationTimestamp();

        if ($accessToken == '') {
            return false;
        }

        $tokenAndTimestampJson = json_encode([
            'token' => $accessToken,
            'token_expiration_timestamp' => $accessTokenExpirationTimestamp
        ]);

        $this->tokenDataEncrypted = $tokenAndTimestampJson;

        return true;
    }

    public function get(): ?SpotifyCredentials
    {
        if (!isset($this->tokenDataEncrypted)) {
            return null;
        }

        $tokenDataEncrypted = $this->tokenDataEncrypted;
        $tokenAndTimestampDataJson = json_decode($tokenDataEncrypted, true);

        return new SpotifyGenericCredentials(
            $tokenAndTimestampDataJson['token'],
            $tokenAndTimestampDataJson['token_expiration_timestamp']
        );
    }
}
