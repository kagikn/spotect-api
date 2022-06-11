<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use _PHPStan_3e014c27f\RingCentral\Psr7\ServerRequest;
use App\Application\Services\GetSpotifyTrackService;
use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackObjectSimplified;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackObjectSimplifiedCustom;
use App\Domain\SpotifyCredentials\SpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;
use App\Infrastructure\Persistence\SpotifyCredentials\InMemoryClientCredentialsRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\FakeClasses\FakeGeoIPDetector;
use Tests\FakeClasses\FakeLogger;
use Tests\FakeClasses\SpotifyApi\FakeAuthApi;
use Tests\FakeClasses\SpotifyApi\FakeTrackRepository;
use Tests\TestCase;

class GetSpotifyTrackServiceTest extends TestCase
{
    public function getTrackServiceProviderFor200ResponseTest(): array
    {
        $tokenFetchingService = $this->mockedTokenFetchingSercice(
            'valid_token'
        );

        return [
            [
                new GetSpotifyTrackService(
                    $tokenFetchingService,
                    new FakeTrackRepository(),
                    new FakeGeoIPDetector()
                )
            ]
        ];
    }

    private function mockedTokenFetchingSercice(string $accessToken
    ): SpotifyClientTokenFetchingService {
        $tokenFetchingService = $this->getMockBuilder(
            SpotifyClientTokenFetchingService::class
        )
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $tokenFetchingService->method('fetch')
            ->willReturn(new SpotifyGenericCredentials($accessToken, 0));

        return $tokenFetchingService;
    }

    public function getTrackServiceProviderFor400ErrorTest(): array
    {
        $tokenFetchingService = $this->mockedTokenFetchingSercice('');

        return [
            [
                new GetSpotifyTrackService(
                    $tokenFetchingService,
                    new FakeTrackRepository(),
                    new FakeGeoIPDetector()
                )
            ]
        ];
    }

    /**
     * @dataProvider getTrackServiceProviderFor400ErrorTest
     */
    public function testCatch400ResponseForInvalidId(
        GetSpotifyTrackService $getTrackService,
    ) {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $app = $this->getAppInstance();
        $app->get(
            '/test-get-spotify-track/{id}',
            array($getTrackService, 'getTrack')
        );
        $request = $this->createRequest('GET', '/test-get-spotify-track/a');
        $response = $app->handle($request);

        $this->assertSame(400, $response->getStatusCode());

        $expectedJsonArray = [
            'error' => [
                'status' => 400,
                'message' => 'invalid id',
            ]
        ];
        $this->assertSame(
            json_encode($expectedJsonArray),
            (string)$response->getBody(),
        );
    }

    /**
     * @dataProvider getTrackServiceProviderFor200ResponseTest
     */
    public function testReturnJsonBodyWithMusicAttributesFor200Response(
        GetSpotifyTrackService $getTrackService,
    ) {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $app = $this->getAppInstance();
        $app->get(
            '/test-get-spotify-track/{id}',
            array($getTrackService, 'getTrack')
        );
        $request = $this->createRequest('GET', '/test-get-spotify-track/a');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $expectedJsonBody =
            TrackObjectSimplifiedCustom::fromTrackObjectFull(
                TrackObjectFullEntity::fromTrackObjItemArray(
                    json_decode(FakeTrackRepository::getENJsonBody(), true)
                )
            )->toJson();
        $this->assertSame(
            $expectedJsonBody,
            (string)$response->getBody()
        );
    }
}
