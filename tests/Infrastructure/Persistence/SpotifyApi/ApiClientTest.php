<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\SpotifyApi;

use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Exception\SpotifyApi\BadOAuthRequestException;
use App\Exception\SpotifyApi\BadRequestParameterException;
use App\Exception\SpotifyApi\InvalidTokenException;
use App\Exception\SpotifyApi\RateLimitExceededException;
use App\Exception\SpotifyApi\SpotifyApiException;
use App\Domain\SpotifyCredentials\SpotifyAuthApi;
use App\Infrastructure\Persistence\SpotifyApi\ApiClient;
use App\Infrastructure\Persistence\SpotifyCredentials\InMemoryClientCredentialsRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Tests\FakeClasses\FakeLogger;
use Tests\TestCase;

class ApiClientTest extends TestCase
{
    // not covered when the response body is empty, which json_decode returns null for
    public function guzzleClientProviderForSuccessfulResponse(): array
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"danceability":0.502}',
            )
        ]);

        $handlerStack = HandlerStack::create($mock);
        return [
            [new ApiClient(new Client(['handler' => $handlerStack]))]
        ];
    }

    public function guzzleClientProviderFor400Response(): array
    {
        $mock = new MockHandler([
            new Response(
                400,
                ['Content-Type' => 'application/json'],
                '{"error":{"status":400,"message":"Missing parameter type"}}'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return [
            [new ApiClient(new Client(['handler' => $handlerStack]))]
        ];
    }

    public function guzzleClientProviderFor401Response(): array
    {
        $mock = new MockHandler([
            new Response(
                401,
                ['Content-Type' => 'application/json'],
                '{"error":{"status":401,"message":"The access token expired"}}'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return [
            [new ApiClient(new Client(['handler' => $handlerStack]))]
        ];
    }

    public function guzzleClientProviderFor403Response(): array
    {
        $mock = new MockHandler([
            new Response(
                403,
                ['Content-Type' => 'application/json'],
                '{"error":{"status":403,"message":"Invalid oauth request"}}'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return [
            [new ApiClient(new Client(['handler' => $handlerStack]))]
        ];
    }

    public function guzzleClientProviderFor429Response(): array
    {
        $mock = new MockHandler([
            new Response(
                429,
                ['Content-Type' => 'application/json'],
                '{"error":{"status":429,"message":"Exceeded API limit request"}}'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return [
            [new ApiClient(new Client(['handler' => $handlerStack]))]
        ];
    }

    public function guzzleClientProviderForNetworkError(): array
    {
        $mock = new MockHandler([
            new ConnectException('Dummy', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        return [
            [new ApiClient(new Client(['handler' => $handlerStack]))]
        ];
    }

    /**
     * @dataProvider guzzleClientProviderForSuccessfulResponse
     */
    public function testSuccessfulResponse(ApiClient $client)
    {
        $resultArray = $client->get('audio-feature', '');
        $this->assertIsArray($resultArray);
    }

    /**
     * @dataProvider guzzleClientProviderFor400Response
     */
    public function testThrowBadRequestParameterExceptionFor400Response(
        ApiClient $client
    ) {
        $this->expectException(BadRequestParameterException::class);
        $client->get('audio-feature', '');
    }

    /**
     * @dataProvider guzzleClientProviderFor401Response
     */
    public function testThrowInvalidTokenExceptionFor401Response(
        ApiClient $client
    ) {
        $this->expectException(InvalidTokenException::class);
        $client->get('audio-feature', '');
    }

    /**
     * @dataProvider guzzleClientProviderFor403Response
     */
    public function testThrowBadOAuthRequestExceptionFor403Response(
        ApiClient $client
    ) {
        $this->expectException(BadOAuthRequestException::class);
        $client->get('audio-feature', '');
    }

    /**
     * @dataProvider guzzleClientProviderFor429Response
     */
    public function testThrowRateLimitExceededExceptionFor429Response(
        ApiClient $client
    ) {
        $this->expectException(RateLimitExceededException::class);
        $client->get('audio-feature', '');
    }

    /**
     * @dataProvider guzzleClientProviderFor400Response
     */
    public function testApiMessageAndEndpointUriOfSpotifyApiErrorResponse(
        ApiClient $client
    ) {
        try {
            $client->get('audio-feature', '');
            $this->fail();
        } catch (SpotifyApiException $ex) {
            $this->assertSame('Missing parameter type', $ex->getApiMessage());
            $this->assertSame('audio-feature', $ex->getEndpointUri());
        }
    }

    /**
     * @dataProvider guzzleClientProviderForNetworkError
     */
    public function testNetworkErrorResponse(ApiClient $client)
    {
        $this->expectException(ConnectException::class);
        $client->get('audio-feature', '');
    }
}
