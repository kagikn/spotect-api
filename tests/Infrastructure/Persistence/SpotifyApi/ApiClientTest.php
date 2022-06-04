<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\Entities\SpotifyApi\ErrorResponse;
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

    public function guzzleClientProviderForErrorResponse(): array
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
     * @dataProvider guzzleClientProviderForErrorResponse
     */
    public function testBadRequestErrorResponse(ApiClient $client)
    {
        $errorRes = $client->get('audio-feature', '');
        $this->assertInstanceOf(ErrorResponse::class, $errorRes);
        $httpStatus = $errorRes->httpStatus;
        $this->assertTrue($httpStatus < 200 || $httpStatus > 299);
    }

    /**
     * @dataProvider guzzleClientProviderForNetworkError
     */
    public function testNetworkErrorResponse(ApiClient $client)
    {
        $errorRes = $client->get('audio-feature', '');
        $this->assertInstanceOf(ErrorResponse::class, $errorRes);
        $this->assertEquals(0, $errorRes->httpStatus);
    }
}
