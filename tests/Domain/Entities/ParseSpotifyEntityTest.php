<?php

declare(strict_types=1);

namespace Tests\Domain\Entities;

use App\Domain\Entities\SpotifyApi\AlbumObjectSimplified;
use App\Domain\Entities\SpotifyApi\ArtistObjectSimplified;
use App\Domain\Entities\SpotifyApi\ImageObject;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackObjectSimplifiedCustom;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackPagingObjectSimplified;
use Tests\TestCase;

class ParseSpotifyEntityTest extends TestCase
{
    public function trackPagingObjectProvider(): array
    {
        $trackItems = $this->trackObjectFullEntityProvider()[0][0];

        return [
            [
                [
                    'href' => '',
                    'items' => $trackItems,
                    'limit' => 0,
                    'offset' => 0,
                    'total' => 1,
                    'next' => null,
                    'previous' => null
                ]
            ]
        ];
    }

    public function trackObjectFullEntityProvider(): array
    {
        $artistsArray = $this->artistsArrayProvider()[0][0];
        $albumArray = $this->albumArrayProvider()[0][0];
        $trackArray = [
            'id' => 'sdfoadsrert4rthyhgfiorgeruyr5b',
            'album' => $albumArray,
            'artists' => $artistsArray,
            'disc_number' => '1',
            'duration_ms' => '180000',
            'explicit' => false,
            'external_ids' => ['ISDN' => '182023321'],
            'external_urls' => ['spotify' => 'https://spotect.com/track/sdree3eer432'],
            'href' => 'https://spotect.com/track/sdree3eer432',
            'name' => 'some track shit',
            'popularity' => 98,
            'track_number' => 1,
            'uri' => 'sdree3eer432',
            'is_local' => false,
            'is_playable' => true,
            'linked_from' => null,
            'preview_url' => null,
            'restrictions' => null,
        ];

        return [[[$trackArray]]];
    }

    public function artistsArrayProvider(): array
    {
        $firstArtistsArray = [
            [
                'id' => 'adfdadf',
                'external_urls' => ['spotify' => 'https://spotect.com'],
                'href' => 'https://spotect.com',
                'name' => 'some name',
                'type' => 'artist',
                'uri' => 'spotify:' . 'adfdadf',
            ]
        ];

        return [[$firstArtistsArray]];
    }

    public function albumArrayProvider(): array
    {
        $imageArray = $this->imageObjectArrayProvider()[0][0];
        $artistsArray = $this->artistsArrayProvider()[0][0];

        $albumArray = [
            'id' => 'adssfdaeawretljo',
            'album_type' => 'compi',
            'artists' => $artistsArray,
            'external_urls' => ['spotify' => 'https://spotect.com'],
            'href' => 'https://spotect.com/someshot',
            'images' => $imageArray,
            'name' => 'No name heck',
            'release_date' => '2021-12-31',
            'release_date_precision' => 'day',
            'total_tracks' => 8,
            'type' => 'album',
            'uri' => 'shoeroawerjeawoj',
            'album_group' => 'compilation',
            'available_markets' => ['US', 'JP'],
            'restrictions' => null,
        ];

        return [
            [
                $albumArray
            ]
        ];
    }

    public function imageObjectArrayProvider(): array
    {
        $firstImgArray = [
            [
                'height' => 600,
                'width' => 600,
                'url' => 'https://i.spotect.com/xClu3xzhrC9p_TH7yS_rjWT66jxNZ-oZ_hn9WdWC7v9ukBuX3j8ivw'
            ],
            [
                'height' => 300,
                'width' => 300,
                'url' => 'https://i.spotect.com/xClu3xzhrC9p_TH7yS_rjWT66jxNZ-oZ_hn9WdWC7v9ukBuX3j8ivw'
            ],
            [
                'height' => 64,
                'width' => 64,
                'url' => 'https://i.spotect.com/xClu3xzhrC9p_TH7yS_rjWT66jxNZ-oZ_hn9WdWC7v9ukBuX3j8ivw'
            ],
        ];

        return [[$firstImgArray]];
    }

    public function trackArrayProviderWithoutAlbumAndArtists(): array
    {
        $trackArray = [
            'id' => 'sdfoadsrert4rthyhgfiorgeruyr5b',
            'disc_number' => '1',
            'duration_ms' => '180000',
            'explicit' => false,
            'external_ids' => ['ISDN' => '182023321'],
            'external_urls' => ['spotify' => 'https://spotect.com/track/sdree3eer432'],
            'href' => 'https://spotect.com/track/sdree3eer432',
            'name' => 'some track shit',
            'popularity' => 98,
            'track_number' => 1,
            'uri' => 'sdree3eer432',
            'is_local' => false,
            'is_playable' => true,
            'linked_from' => null,
            'preview_url' => null,
            'restrictions' => null,
        ];

        return [
            [
                [
                    'height' => 600,
                    'width' => 600,
                    'url' => 'https://i.spotect.com/mRQByf0TiIc6CEXMm1D7uRmbf5wQVZMOcppn_Li0ZN4TqeaB0VQQZg'
                ],
                [
                    'height' => 300,
                    'width' => 300,
                    'url' => 'https://i.spotect.com/mRQByf0TiIc6CEXMm1D7uRmbf5wQVZMOcppn_Li0ZN4TqeaB0VQQZg'
                ],
                [
                    'height' => 64,
                    'width' => 64,
                    'url' => 'https://i.spotect.com/mRQByf0TiIc6CEXMm1D7uRmbf5wQVZMOcppn_Li0ZN4TqeaB0VQQZg'
                ],
            ]
        ];
    }

    /**
     * @dataProvider imageObjectArrayProvider
     */
    public function testJsonDeserializeImageObjectArray(array $responseArray)
    {
        $imageObjs = ImageObject::fromImageObjectItemsArrayOfResponse(
            $responseArray
        );
        $this->assertIsArray($imageObjs);
        $this->assertInstanceOf(ImageObject::class, $imageObjs[0]);
    }

    /**
     * @dataProvider artistsArrayProvider
     */
    public function testJsonDeserializeArtistObjectSimplified(
        array $responseJsonArray
    ) {
        $artistObjs = ArtistObjectSimplified::fromItemCollectionArray(
            $responseJsonArray
        );
        $this->assertIsArray($artistObjs);
        $this->assertInstanceOf(ArtistObjectSimplified::class, $artistObjs[0]);
    }

    /**
     * @dataProvider albumArrayProvider
     * @depends      testJsonDeserializeImageObjectArray
     * @depends      testJsonDeserializeArtistObjectSimplified
     * Checks if two depending functions work before testing this function.
     * The "depends" annotations are used only to check if the depending
     *     functions work, which AlbumObjectSimplified::fromItemArrayOfResponse
     *     calls.
     */
    public function testJsonDeserializeAlbumObjectSimplified(
        array $responseJsonArray
    ) {
        $albumObj = AlbumObjectSimplified::fromItemArray(
            $responseJsonArray
        );
        $this->assertInstanceOf(AlbumObjectSimplified::class, $albumObj);
    }

    /**
     * @dataProvider trackObjectFullEntityProvider
     * @depends      testJsonDeserializeImageObjectArray
     * @depends      testJsonDeserializeArtistObjectSimplified
     * @depends      testJsonDeserializeAlbumObjectSimplified
     * Checks if all the three depending functions work before testing this
     *     function. The "depends" annotations are used only to check if all
     *     the depending functions work, which
     *     AlbumObjectSimplified::fromItemArrayOfResponse calls.
     */
    public function testJsonDeserializeTrackObjectFullEntity(
        array $responseJsonArray
    ): array {
        $trackObjs = TrackObjectFullEntity::fromItemsArrayOfResponse(
            $responseJsonArray
        );
        $this->assertIsArray($trackObjs);
        $this->assertEquals(1, count($trackObjs));
        $this->assertInstanceOf(TrackObjectFullEntity::class, $trackObjs[0]);

        return $trackObjs;
    }

    /**
     * @dataProvider trackObjectFullEntityProvider
     * @depends      testJsonDeserializeTrackObjectFullEntity
     */
    public function testConvertTrackObjFullToTrackObjSimplifiedCustom(
        array $responseJsonArray
    ) {
        $trackObjs = TrackObjectFullEntity::fromItemsArrayOfResponse(
            $responseJsonArray
        );
        $trackObj = TrackObjectSimplifiedCustom::fromTrackObjectFull(
            $trackObjs[0]
        );
        $this->assertInstanceOf(TrackObjectSimplifiedCustom::class, $trackObj);
    }

    /**
     * @dataProvider trackPagingObjectProvider
     * @depends      testJsonDeserializeImageObjectArray
     * @depends      testJsonDeserializeArtistObjectSimplified
     * @depends      testJsonDeserializeAlbumObjectSimplified
     * @depends      testJsonDeserializeTrackObjectFullEntity
     * Checks if all the three depending functions work before testing this
     *     function. The "depends" annotations are used only to check if all
     *     the depending functions work, which
     *     AlbumObjectSimplified::fromItemArrayOfResponse calls.
     */
    public function testJsonDeserializeTrackSearchResponse(
        array $responseJsonArray
    ) {
        $trackObjs = TrackPagingObject::fromTrackSearchResponse(
            $responseJsonArray
        );
        $this->assertInstanceOf(TrackPagingObject::class, $trackObjs);
    }

    /**
     * @dataProvider trackPagingObjectProvider
     * @depends      testJsonDeserializeTrackSearchResponse
     */
    public function testJsonDeserializeTrackPagingObjectSimplified(
        array $responseJsonArray
    ) {
        $trackObjs = TrackPagingObject::fromTrackSearchResponse(
            $responseJsonArray
        );
        $trackObj = TrackPagingObjectSimplified::fromTrackPagingObjectFull(
            $trackObjs
        );
        $this->assertInstanceOf(TrackPagingObjectSimplified::class, $trackObj);
    }
}
