<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use _PHPStan_3e014c27f\RingCentral\Psr7\ServerRequest;
use App\Application\Services\FetchSpotifyAudioFeatureService;
use App\Application\Services\GetSpotifyTrackService;
use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackObjectSimplified;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackObjectSimplifiedCustom;
use App\Domain\SpotifyApi\AudioFeatureRepository;
use App\Domain\SpotifyCredentials\SpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;
use App\Exception\SpotifyApi\BadRequestParameterException;
use App\Infrastructure\Persistence\SpotifyApi\AudioFeatureCacheRepository;
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

class FetchSpotifyAudioFeatureServiceTest extends TestCase
{
    const JSON_STRING_TO_TEST = '{
  "danceability": 0.696,
  "energy": 0.905,
  "key": 2,
  "loudness": -2.743,
  "mode": 1,
  "speechiness": 0.103,
  "acousticness": 0.011,
  "instrumentalness": 0.000905,
  "liveness": 0.302,
  "valence": 0.625,
  "tempo": 114.944,
  "type": "audio_features",
  "id": "11dFghVXANMlKmJXsNCbNl",
  "uri": "spotify:track:11dFghVXANMlKmJXsNCbNl",
  "track_href": "https://api.spotify.com/v1/tracks/11dFghVXANMlKmJXsNCbNl",
  "analysis_url": "https://api.spotify.com/v1/audio-analysis/11dFghVXANMlKmJXsNCbNl",
  "duration_ms": 207960,
  "time_signature": 4
}';

    public function getTrackServiceProviderFor200ResponseTest(): array
    {
        $tokenFetchingService = $this->mockedTokenFetchingSercice(
            'valid_token'
        );

        $audioFeatureRepository = $this->getMockBuilder(
            AudioFeatureRepository::class
        )->getMock();

        $jsonStr = self::JSON_STRING_TO_TEST;
        $audioFeatureRepository->method('getAudioFeature')
            ->willReturn(
                AudioFeaturesObject::fromJson(
                    $jsonStr
                )
            );

        $audioFeatureCacheRepository = $this->getMockBuilder(
            AudioFeatureCacheRepository::class
        )->getMock();

        $audioFeatureCacheRepository->method('get')
            ->willReturn(null);

        return [
            [
                new FetchSpotifyAudioFeatureService(
                    $tokenFetchingService,
                    $audioFeatureRepository,
                    $audioFeatureCacheRepository
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
        $tokenFetchingService = $this->mockedTokenFetchingSercice(
            ''
        );

        $audioFeatureRepository = $this->getMockBuilder(
            AudioFeatureRepository::class
        )->getMock();

        $audioFeatureRepository->method('getAudioFeature')
            ->willThrowException(
                new BadRequestParameterException(
                    'audio-feature',
                    'invalid ddd id'
                )
            );

        $audioFeatureCacheRepository = $this->getMockBuilder(
            AudioFeatureCacheRepository::class
        )->getMock();

        $audioFeatureCacheRepository->method('get')
            ->willReturn(null);

        return [
            [
                new FetchSpotifyAudioFeatureService(
                    $tokenFetchingService,
                    $audioFeatureRepository,
                    $audioFeatureCacheRepository
                )
            ]
        ];
    }

    /**
     * @dataProvider getTrackServiceProviderFor400ErrorTest
     */
    public function testCatch400ResponseForInvalidId(
        FetchSpotifyAudioFeatureService $getTrackService,
    ) {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $app = $this->getAppInstance();
        $app->get(
            '/test-get-audio-feature/{id}',
            array($getTrackService, 'fetchTrackAudioFeature')
        );
        $request = $this->createRequest('GET', '/test-get-audio-feature/a');
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
        FetchSpotifyAudioFeatureService $getTrackService,
    ) {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $app = $this->getAppInstance();
        $app->get(
            '/test-get-audio-feature/{id}',
            array($getTrackService, 'fetchTrackAudioFeature')
        );
        $request = $this->createRequest('GET', '/test-get-audio-feature/a');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $jsonStr = self::JSON_STRING_TO_TEST;
        $expectedJsonBody = AudioFeaturesObject::fromJson($jsonStr)
            ->mainValuesToJson();

        $this->assertSame(
            $expectedJsonBody,
            (string)$response->getBody()
        );
    }
}
