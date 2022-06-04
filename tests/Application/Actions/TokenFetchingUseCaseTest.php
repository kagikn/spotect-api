<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\SpotifyCredentials\SpotifyAuthApi;
use App\Infrastructure\Persistence\SpotifyCredentials\InMemoryClientCredentialsRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Tests\FakeClasses\FakeLogger;
use Tests\TestCase;

class TokenFetchingUseCaseTest extends TestCase
{
    public function guzzleClientProvider(): Client
    {
        $bodyToDisableCachingToken = '{"access_token":"",' .
            '"token_type":"bearer",' .
            '"expires_in":0}';

        $bodyTexts = '{"access_token":"p7XM174PIk75Vo6HDX9X-7LGPKtG_Q1KxPGFmK2nWplqam0SMA2DSGlhP78H9yNZzTPORV3x_' .
            'CsWfF-yqzXcarJaJIYE7JkhwjYb53P5-Gi7enc1BYEEB6XkAJ-2I940k6nq_W8x1kQ4ksGo5HiTqa6M0PK5jQupfO1AJIfZth24' .
            'WxyNjZ5yX5IebVVUjV-vUAPzbQ",' .
            '"token_type":"bearer",' .
            '"expires_in":3600}';

        $bodyTexts2 = '{"access_token":"cyfUXfJOrmFFRmtiKEKcUU7iSDT_TDxW6pduu20vwtfTw76TtE9D72rNeGqoW2TwSTrP8wpa' .
            'h7f4hOstB2XT-dAQsyl8xSqX6WGKSzMAwhdgVl0JNRZHXU5QLeiI5SaR_zwjyJudxvGZrEYFgEpBHDY0e06WFiNaWzBTRGdDeE0' .
            '21aHODBQmofNwh26p1PafiQJQFA",' .
            '"token_type":"bearer",' .
            '"expires_in":3600}';

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $bodyTexts),
            new Response(200, ['Content-Type' => 'application/json'], $bodyTexts2),
            new Response(200, ['Content-Type' => 'application/json'], $bodyToDisableCachingToken),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }

    public function spotifyAuthApiProvider(): SpotifyAuthApi
    {
        $guzzleClient = $this->guzzleClientProvider();

        return new SpotifyAuthApi($guzzleClient);
    }

    public function tokenFetchingServiceProvider(): array
    {
        $authApiProvider = [$this->spotifyAuthApiProvider()];

        return [
            $authApiProvider
        ];
    }

    public function authApiForEmptyTokenProvider(): array
    {
        $bodyToDisableCachingToken = '{"access_token":"",' .
            '"token_type":"bearer",' .
            '"expires_in":0}';

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $bodyToDisableCachingToken),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        $authApiProvider = [new SpotifyAuthApi($guzzleClient)];

        return [
            $authApiProvider
        ];
    }

    public function authApiForErrorResponseProvider(): array
    {
        $bodyToDisableCachingToken = '{"error":"server_error"}';

        $mock = new MockHandler([
            new Response(400, ['Content-Type' => 'application/json'], $bodyToDisableCachingToken),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        $authApiProvider = [new SpotifyAuthApi($guzzleClient)];

        return [
            $authApiProvider
        ];
    }

    /**
     * @dataProvider tokenFetchingServiceProvider
     */
    public function testTokenFetchAndCache(SpotifyAuthApi $authApi)
    {
        $clientCredentialsRepository = new InMemoryClientCredentialsRepository();
        $logger = new FakeLogger();
        $tokenFetchingService = new SpotifyClientTokenFetchingService(
            $authApi,
            $clientCredentialsRepository,
            $logger,
        );

        $firstCredentials = $tokenFetchingService->fetch('', '');
        $firstStoredCredentials = $clientCredentialsRepository->get();
        $this->assertEquals($firstStoredCredentials->getAccessToken(), $firstCredentials->getAccessToken());
        $this->assertEquals(
            $firstStoredCredentials->getAccessTokenExpirationTimestamp(),
            $firstCredentials->getAccessTokenExpirationTimestamp()
        );

        $secondCachedCredentials = $tokenFetchingService->fetch('', '');
        $this->assertEquals($firstStoredCredentials->getAccessToken(), $secondCachedCredentials->getAccessToken());
        $this->assertEquals(
            $firstStoredCredentials->getAccessTokenExpirationTimestamp(),
            $secondCachedCredentials->getAccessTokenExpirationTimestamp()
        );

        $thirdCredentials = $tokenFetchingService->fetch('', '', 7200);
        $this->assertNotEquals($firstStoredCredentials->getAccessToken(), $thirdCredentials->getAccessToken());
    }

    /**
     * @dataProvider authApiForEmptyTokenProvider
     */
    public function testWarningLogForStoringFetchedToken(SpotifyAuthApi $authApi)
    {
        $clientCredentialsRepository = new InMemoryClientCredentialsRepository();
        $logger = new FakeLogger();
        $tokenFetchingService = new SpotifyClientTokenFetchingService(
            $authApi,
            $clientCredentialsRepository,
            $logger,
        );

        $errorMsg = "Token was able to be fetched from Spotify API," .
            "but CredentialsRepository could not store it.";
        $tokenFetchingService->fetch('', '', 7200);
        $this->assertEquals($errorMsg, $logger->getLogLineMessage(1));
    }

    /**
     * @dataProvider authApiForErrorResponseProvider
     */
    public function testErrorWhenFailedToFetchAvailableCachedTokenOrNewToken(SpotifyAuthApi $authApi)
    {
        $clientCredentialsRepository = new InMemoryClientCredentialsRepository();
        $logger = new FakeLogger();
        $tokenFetchingService = new SpotifyClientTokenFetchingService(
            $authApi,
            $clientCredentialsRepository,
            $logger,
        );

        $nullRes = $tokenFetchingService->fetch('', '', 7200);
        $this->assertNull($nullRes);
    }
}
