<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use _PHPStan_3e014c27f\RingCentral\Psr7\ServerRequest;
use App\Application\Services\GetSpotifyTrackService;
use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackObjectSimplifiedCustom;
use App\Domain\SpotifyCredentials\SpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;
use App\Infrastructure\Persistence\SpotifyCredentials\InMemoryClientCredentialsRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\FakeClasses\FakeGeoIPDetector;
use Tests\FakeClasses\FakeLogger;
use Tests\FakeClasses\SpotifyApi\FakeAuthApi;
use Tests\FakeClasses\SpotifyApi\FakeTrackRepository;
use Tests\TestCase;

class GetSpotifyTrackServiceTest extends TestCase
{
    public function testTokenFetchAndCache()
    {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $stub = $this->getMockBuilder(SpotifyClientTokenFetchingService::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $stub->method('fetch')
            ->willThrowException(
                new ClientException(
                    'Fake error',
                    new \GuzzleHttp\Psr7\ServerRequest('GET', 'fake'),
                    new Response(400),
                )
            );

        $trackRepo = new FakeTrackRepository();
        $fakeGeoIPDetector = new FakeGeoIPDetector();

        $inst = new GetSpotifyTrackService(
            $stub,
            $trackRepo,
            $fakeGeoIPDetector
        );

        $app = $this->getAppInstance();
        $app->get('/test-get-spotify-track/{id}', array($inst, 'getTrack'));
        $request = $this->createRequest('GET', '/test-get-spotify-track/a');

        $this->expectException(ClientException::class);
        $app->handle($request);
    }

    public function testAa()
    {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $stub = $this->getMockBuilder(SpotifyClientTokenFetchingService::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $stub->method('fetch')
            ->willReturn(new SpotifyGenericCredentials('a', 0));

        $trackRepo = new FakeTrackRepository();

        $fakeGeoIPDetector = new FakeGeoIPDetector();

        $inst = new GetSpotifyTrackService(
            $stub,
            $trackRepo,
            $fakeGeoIPDetector
        );

        $app = $this->getAppInstance();
        $app->get('/test-get-spotify-track/{id}', array($inst, 'getTrack'));
        $request = $this->createRequest('GET', '/test-get-spotify-track/a');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $trackObjFull = TrackObjectFullEntity::fromTrackObjItemArray(
            json_decode(FakeTrackRepository::getENJsonBody(), true)
        );
        $expectedBody = TrackObjectSimplifiedCustom::fromTrackObjectFull(
            $trackObjFull
        )->toJson();
        $this->assertSame(
            $expectedBody,
            $body
        );
    }

    public function testBb()
    {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $stub = $this->getMockBuilder(SpotifyClientTokenFetchingService::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $stub->method('fetch')
            ->willReturn(new SpotifyGenericCredentials('', 0));

        $trackRepo = new FakeTrackRepository();

        $fakeGeoIPDetector = new FakeGeoIPDetector();

        $inst = new GetSpotifyTrackService(
            $stub,
            $trackRepo,
            $fakeGeoIPDetector
        );

        $app = $this->getAppInstance();
        $app->get('/test-get-spotify-track/{id}', array($inst, 'getTrack'));
        $request = $this->createRequest('GET', '/test-get-spotify-track/b');
        $response = $app->handle($request);

        $this->assertSame(400, $response->getStatusCode());

        $expectedJsonArray = [
            'error' => [
                'status' => 400,
                'message' => 'invalid track parameter',
            ]
        ];

        $this->assertSame(
            json_encode($expectedJsonArray),
            (string)$response->getBody()
        );
    }
}
