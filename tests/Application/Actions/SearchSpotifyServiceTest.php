<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Services\GetSpotifyTrackService;
use App\Application\Services\SearchSpotifyService;
use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackObjectSimplifiedCustom;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackPagingObjectSimplified;
use App\Domain\SpotifyApi\SearchRepository;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;
use App\Exception\SpotifyApi\BadRequestParameterException;
use Tests\FakeClasses\FakeGeoIPDetector;
use Tests\FakeClasses\SpotifyApi\FakeTrackRepository;
use Tests\TestCase;

class SearchSpotifyServiceTest extends TestCase
{
    private const JSON_STRING_TO_TEST =
        '{
            "href":"https://api.spotify.com/v1/search?query=Rick+James&type=track&market=US&locale=ja%2Cen-US%3Bq%3D0.9%2Cen%3Bq%3D0.8&offset=0&limit=1",
            "items":[
                {"album":
                    {"album_type":"album",
                    "artists":[{"external_urls":{"spotify":"https://open.spotify.com/artist/0FrpdcVlJQqibaz5HfBUrL"},"href":"https://api.spotify.com/v1/artists/0FrpdcVlJQqibaz5HfBUrL","id":"0FrpdcVlJQqibaz5HfBUrL","name":"Rick James","type":"artist","uri":"spotify:artist:0FrpdcVlJQqibaz5HfBUrL"}],"external_urls":{"spotify":"https://open.spotify.com/album/2DBFUBBqJQvfXpodPi2WP5"},"href":"https://api.spotify.com/v1/albums/2DBFUBBqJQvfXpodPi2WP5","id":"2DBFUBBqJQvfXpodPi2WP5","images":[{"height":640,"url":"https://i.scdn.co/image/ab67616d0000b27317f9e7e7784ed40b223e261c","width":640},{"height":300,"url":"https://i.scdn.co/image/ab67616d00001e0217f9e7e7784ed40b223e261c","width":300},{"height":64,"url":"https://i.scdn.co/image/ab67616d0000485117f9e7e7784ed40b223e261c","width":64}],
                    "name":"Street Songs (Deluxe Edition)",
                    "release_date":"1981-04-07","release_date_precision":"day",
                    "total_tracks":25,
                    "type":"album",
                    "uri":"spotify:album:2DBFUBBqJQvfXpodPi2WP5"
                },
                "artists":[{"external_urls":{"spotify":"https://open.spotify.com/artist/0FrpdcVlJQqibaz5HfBUrL"},"href":"https://api.spotify.com/v1/artists/0FrpdcVlJQqibaz5HfBUrL","id":"0FrpdcVlJQqibaz5HfBUrL","name":"Rick James","type":"artist","uri":"spotify:artist:0FrpdcVlJQqibaz5HfBUrL"}],
                "disc_number":1,
                "duration_ms":205466,
                "explicit":false,
                "external_ids":{"isrc":"USMO18100048"},"external_urls":{"spotify":"https://open.spotify.com/track/2dCmGcEOQrMQhMMS8Vj7Ca"},
                "href":"https://api.spotify.com/v1/tracks/2dCmGcEOQrMQhMMS8Vj7Ca",
                "id":"2dCmGcEOQrMQhMMS8Vj7Ca",
                "is_local":false,"is_playable":true,
                "name":"Super Freak",
                "popularity":72,
                "preview_url":null,
                "track_number":5,
                "type":"track",
                "uri":"spotify:track:2dCmGcEOQrMQhMMS8Vj7Ca"
            }],
            "limit":1,
            "next":"https://api.spotify.com/v1/search?query=Rick+James&type=track&market=US&locale=ja%2Cen-US%3Bq%3D0.9%2Cen%3Bq%3D0.8&offset=1&limit=1",
            "offset":0,
            "previous":null,
            "total":2740
        }';

    public function getTrackServiceProviderFor200Response(): array
    {
        $tokenFetchingService = $this->mockedTokenFetchingSerciceProvider(
            'valid_token'
        );
        $searchRepository = $this->mockedSuccessfulSearchRepositoryProvider();

        return [
            [
                new SearchSpotifyService(
                    $tokenFetchingService,
                    $searchRepository,
                    new FakeGeoIPDetector()
                )
            ]
        ];
    }

    private function mockedTokenFetchingSerciceProvider(string $accessToken
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

    private function mockedSuccessfulSearchRepositoryProvider(
    ): SearchRepository
    {
        $tokenFetchingService = $this->getMockBuilder(
            SearchRepository::class
        )
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $tokenFetchingService->method('searchForTrack')
            ->willReturn(
                TrackPagingObject::fromTrackSearchResponse(
                    json_decode(self::JSON_STRING_TO_TEST, true)
                )
            );

        return $tokenFetchingService;
    }

    public function getTrackServiceProviderFor400ErrorTest(): array
    {
        $tokenFetchingService = $this->mockedTokenFetchingSerciceProvider('');
        $searchRepository = $this->mockedFailureSpotifyRepositoryProvider();

        return [
            [
                new SearchSpotifyService(
                    $tokenFetchingService,
                    $searchRepository,
                    new FakeGeoIPDetector()
                )
            ]
        ];
    }

    private function mockedFailureSpotifyRepositoryProvider(): SearchRepository
    {
        $searchRepository = $this->getMockBuilder(
            SearchRepository::class
        )
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $searchRepository->method('searchForTrack')
            ->willThrowException(
                new BadRequestParameterException('search', 'No search query')
            );

        return $searchRepository;
    }

    /**
     * @dataProvider getTrackServiceProviderFor400ErrorTest
     */
    public function testCatch400ResponseForInvalidId(
        SearchSpotifyService $getTrackService,
    ) {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $app = $this->getAppInstance();
        $app->get(
            '/test-search',
            array($getTrackService, 'search')
        );
        $request = $this->createRequest('GET', '/test-search');
        $response = $app->handle($request);

        $this->assertSame(400, $response->getStatusCode());

        $expectedJsonArray = [
            'error' => [
                'status' => 400,
                'message' => 'No search query',
            ]
        ];
        $this->assertSame(
            json_encode($expectedJsonArray),
            (string)$response->getBody(),
        );
    }

    /**
     * @dataProvider getTrackServiceProviderFor200Response
     */
    public function testReturnJsonBodyWithMusicAttributesFor200Response(
        SearchSpotifyService $getTrackService,
    ) {
        $_ENV['SPOTIFY_CLIENT_ID'] = 'a';
        $_ENV['SPOTIFY_CLIENT_SECRET'] = 'b';

        $app = $this->getAppInstance();
        $app->get(
            '/test-search',
            array($getTrackService, 'search')
        );
        $request = $this->createRequest('GET', '/test-search');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $trackPagingObjFull = TrackPagingObject::fromTrackSearchResponse(
            json_decode(self::JSON_STRING_TO_TEST, true)
        );
        $trackPagingObjSimplified = TrackPagingObjectSimplified::fromTrackPagingObjectFull(
            $trackPagingObjFull
        );
        $expectedJsonBody = $trackPagingObjSimplified->toJson();

        $this->assertSame(
            $expectedJsonBody,
            (string)$response->getBody()
        );
    }
}
