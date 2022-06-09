<?php

declare(strict_types=1);

namespace Tests\FakeClasses\SpotifyApi;

use App\Exception\SpotifyApi\BadRequestParameterException;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\SpotifyApi\TrackRepository;

class FakeTrackRepository implements TrackRepository
{
    public function __construct()
    {
    }

    /**
     * @param  string  $trackId
     * @param  string  $accessToken
     * @param ?string  $market
     * @param ?string  $acceptLanguageHeader
     *
     * @return TrackObjectFullEntity
     * @throws BadRequestParameterException
     */
    public function getTrackInfo(
        string $trackId,
        string $accessToken,
        string $market = null,
        string $acceptLanguageHeader = null
    ): TrackObjectFullEntity {
        if (empty($trackId) || empty($accessToken)) {
            throw new BadRequestParameterException(
                'tracks/' . $trackId,
                'invalid track parameter'
            );
        }

        if ($market != 'JP' && $market != 'US') {
            throw new  BadRequestParameterException(
                'Fake error',
                'tracks/' . $trackId,
                'invalid market parameter'
            );
        }

        if (isset($acceptLanguageHeader) && $acceptLanguageHeader[0] == 'JP') {
            return TrackObjectFullEntity::fromTrackObjItemArray(
                json_decode(self::getJPJsonBody(), true)
            );
        }

        return TrackObjectFullEntity::fromTrackObjItemArray(
            json_decode(self::getENJsonBody(), true)
        );
    }

    public static function getJPJsonBody()
    {
        return '{
  "album": {
    "album_type": "single",
    "artists": [
      {
        "external_urls": {
          "spotify": "https://open.spotify.com/artist/6sFIWsNpZYqfjUpaCgueju"
        },
        "href": "https://api.spotify.com/v1/artists/6sFIWsNpZYqfjUpaCgueju",
        "id": "6sFIWsNpZYqfjUpaCgueju",
        "name": "Carly Rae Jepsen",
        "type": "artist",
        "uri": "spotify:artist:6sFIWsNpZYqfjUpaCgueju"
      }
    ],
    "external_urls": {
      "spotify": "https://open.spotify.com/album/0tGPJ0bkWOUmH7MEOR77qc"
    },
    "href": "https://api.spotify.com/v1/albums/0tGPJ0bkWOUmH7MEOR77qc",
    "id": "0tGPJ0bkWOUmH7MEOR77qc",
    "images": [
      {
        "height": 640,
        "url": "https://i.scdn.co/image/ab67616d0000b2737359994525d219f64872d3b1",
        "width": 640
      },
      {
        "height": 300,
        "url": "https://i.scdn.co/image/ab67616d00001e027359994525d219f64872d3b1",
        "width": 300
      },
      {
        "height": 64,
        "url": "https://i.scdn.co/image/ab67616d000048517359994525d219f64872d3b1",
        "width": 64
      }
    ],
    "name": "カットトゥザフィーリング",
    "release_date": "2017-05-26",
    "release_date_precision": "day",
    "total_tracks": 1,
    "type": "album",
    "uri": "spotify:album:0tGPJ0bkWOUmH7MEOR77qc"
  },
  "artists": [
    {
      "external_urls": {
        "spotify": "https://open.spotify.com/artist/6sFIWsNpZYqfjUpaCgueju"
      },
      "href": "https://api.spotify.com/v1/artists/6sFIWsNpZYqfjUpaCgueju",
      "id": "6sFIWsNpZYqfjUpaCgueju",
      "name": "Carly Rae Jepsen",
      "type": "artist",
      "uri": "spotify:artist:6sFIWsNpZYqfjUpaCgueju"
    }
  ],
  "disc_number": 1,
  "duration_ms": 207946,
  "explicit": false,
  "external_ids": {
    "isrc": "USUM71703861"
  },
  "external_urls": {
    "spotify": "https://open.spotify.com/track/7ANmgFJ8YDBh0uUOfSeYrX"
  },
  "href": "https://api.spotify.com/v1/tracks/7ANmgFJ8YDBh0uUOfSeYrX",
  "id": "7ANmgFJ8YDBh0uUOfSeYrX",
  "is_local": false,
  "is_playable": true,
  "linked_from": {
    "external_urls": {
      "spotify": "https://open.spotify.com/track/11dFghVXANMlKmJXsNCbNl"
    },
    "href": "https://api.spotify.com/v1/tracks/11dFghVXANMlKmJXsNCbNl",
    "id": "11dFghVXANMlKmJXsNCbNl",
    "type": "track",
    "uri": "spotify:track:11dFghVXANMlKmJXsNCbNl"
  },
  "name": "カットトゥザフィーリング",
  "popularity": 44,
  "preview_url": "https://p.scdn.co/mp3-preview/4e69d142cceaca1fa4bc8db7a319ab7a0b8ffd82?cid=774b29d4f13844c495f206cafdad9c86",
  "track_number": 1,
  "type": "track",
  "uri": "spotify:track:7ANmgFJ8YDBh0uUOfSeYrX"
}';
    }

    public static function getENJsonBody()
    {
        return '{
  "album": {
    "album_type": "single",
    "artists": [
      {
        "external_urls": {
          "spotify": "https://open.spotify.com/artist/6sFIWsNpZYqfjUpaCgueju"
        },
        "href": "https://api.spotify.com/v1/artists/6sFIWsNpZYqfjUpaCgueju",
        "id": "6sFIWsNpZYqfjUpaCgueju",
        "name": "Carly Rae Jepsen",
        "type": "artist",
        "uri": "spotify:artist:6sFIWsNpZYqfjUpaCgueju"
      }
    ],
    "external_urls": {
      "spotify": "https://open.spotify.com/album/0tGPJ0bkWOUmH7MEOR77qc"
    },
    "href": "https://api.spotify.com/v1/albums/0tGPJ0bkWOUmH7MEOR77qc",
    "id": "0tGPJ0bkWOUmH7MEOR77qc",
    "images": [
      {
        "height": 640,
        "url": "https://i.scdn.co/image/ab67616d0000b2737359994525d219f64872d3b1",
        "width": 640
      },
      {
        "height": 300,
        "url": "https://i.scdn.co/image/ab67616d00001e027359994525d219f64872d3b1",
        "width": 300
      },
      {
        "height": 64,
        "url": "https://i.scdn.co/image/ab67616d000048517359994525d219f64872d3b1",
        "width": 64
      }
    ],
    "name": "Cut To The Feeling",
    "release_date": "2017-05-26",
    "release_date_precision": "day",
    "total_tracks": 1,
    "type": "album",
    "uri": "spotify:album:0tGPJ0bkWOUmH7MEOR77qc"
  },
  "artists": [
    {
      "external_urls": {
        "spotify": "https://open.spotify.com/artist/6sFIWsNpZYqfjUpaCgueju"
      },
      "href": "https://api.spotify.com/v1/artists/6sFIWsNpZYqfjUpaCgueju",
      "id": "6sFIWsNpZYqfjUpaCgueju",
      "name": "Carly Rae Jepsen",
      "type": "artist",
      "uri": "spotify:artist:6sFIWsNpZYqfjUpaCgueju"
    }
  ],
  "disc_number": 1,
  "duration_ms": 207946,
  "explicit": false,
  "external_ids": {
    "isrc": "USUM71703861"
  },
  "external_urls": {
    "spotify": "https://open.spotify.com/track/7ANmgFJ8YDBh0uUOfSeYrX"
  },
  "href": "https://api.spotify.com/v1/tracks/7ANmgFJ8YDBh0uUOfSeYrX",
  "id": "7ANmgFJ8YDBh0uUOfSeYrX",
  "is_local": false,
  "is_playable": true,
  "linked_from": {
    "external_urls": {
      "spotify": "https://open.spotify.com/track/11dFghVXANMlKmJXsNCbNl"
    },
    "href": "https://api.spotify.com/v1/tracks/11dFghVXANMlKmJXsNCbNl",
    "id": "11dFghVXANMlKmJXsNCbNl",
    "type": "track",
    "uri": "spotify:track:11dFghVXANMlKmJXsNCbNl"
  },
  "name": "Cut To The Feeling",
  "popularity": 44,
  "preview_url": "https://p.scdn.co/mp3-preview/4e69d142cceaca1fa4bc8db7a319ab7a0b8ffd82?cid=774b29d4f13844c495f206cafdad9c86",
  "track_number": 1,
  "type": "track",
  "uri": "spotify:track:7ANmgFJ8YDBh0uUOfSeYrX"
}';
    }
}
