<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\SpotifyCredentials\ISpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyCredentials;
use App\Domain\SpotifyCredentials\SpotifyCredentialsRepository;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class SpotifyClientTokenFetchingService
{
    protected ISpotifyAuthApi $authApi;
    protected SpotifyCredentialsRepository $credentialsRepository;
    protected LoggerInterface $logger;

    public function __construct(
        ISpotifyAuthApi $authApi,
        SpotifyCredentialsRepository $credentialsRepository,
        LoggerInterface $logger,
    ) {
        $this->authApi = $authApi;
        $this->credentialsRepository = $credentialsRepository;
        $this->logger = $logger;
    }

    /**
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  int|null  $refreshOffsetTimeBeforeTimeout
     *
     * @return SpotifyCredentials
     * @throws GuzzleException
     */
    public function fetch(
        string $clientId,
        string $clientSecret,
        ?int $refreshOffsetTimeBeforeTimeout = 300
    ): SpotifyCredentials {
        $timeoutUnixTimestamp = time() + $refreshOffsetTimeBeforeTimeout;

        $cachedToken = $this->credentialsRepository->get();
        if (
            isset($cachedToken)
            && $cachedToken->getAccessTokenExpirationTimestamp()
            > $timeoutUnixTimestamp
        ) {
            return $cachedToken;
        }

        try {
            $tokenOrErrorRes = $this->authApi->getTokenClientCredentials(
                $clientId,
                $clientSecret
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Could not fetch client access token.' .
                'The client ID or client secret are wrong or Auth API of Spotify is down.'
            );
            throw $e;
        }

        $token = $tokenOrErrorRes;
        if (!$this->credentialsRepository->store($token)) {
            $this->logger->warning(
                "Token was able to be fetched from Spotify API," .
                "but CredentialsRepository could not store it."
            );
        }
        return $token;
    }
}
