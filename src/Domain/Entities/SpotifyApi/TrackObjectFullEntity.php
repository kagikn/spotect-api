<?php

namespace App\Domain\Entities\SpotifyApi;

class TrackObjectFullEntity extends TrackObjectSimplified
{
    public function __construct(
        string $id,
        public readonly AlbumObjectSimplified $album,
        array $artists,
        int $discNumber,
        int $durationMs,
        bool $explicit,
        public readonly array $externalIds,
        array $externalUrls,
        string $href,
        string $name,
        public readonly int $popularity,
        int $trackNumber,
        string $uri,
        array $availableMarkets = null,
        public readonly bool $isLocal = false,
        bool $isPlayable = false,
        TrackLinkObject $linkedFrom = null,
        string $previewUrl = null,
        RestrictionsObject $restrictions = null,
    ) {
        parent::__construct(
            $id,
            $artists,
            $discNumber,
            $durationMs,
            $explicit,
            $externalUrls,
            $href,
            $name,
            $trackNumber,
            $uri,
            $availableMarkets,
            $isPlayable,
            $linkedFrom,
            $previewUrl,
            $restrictions
        );
    }

    /**
     * @param  array  $trackObjs
     *
     * @return TrackObjectFullEntity[]
     */
    public static function fromItemsArrayOfResponse(array $trackObjs): array
    {
        return array_map(
            fn($trackObj) => self::fromTrackObjItemArray($trackObj),
            $trackObjs
        );
    }

    /**
     * @param  array  $trackObj
     *
     * @return TrackObjectFullEntity
     */
    public static function fromTrackObjItemArray(
        array $trackObj
    ): TrackObjectFullEntity {
        $albumObjSimplified = AlbumObjectSimplified::fromItemArray(
            $trackObj['album']
        );
        $artistObjSimplified = ArtistObjectSimplified::fromItemCollectionArray(
            $trackObj['artists']
        );
        $restrictionObj = isset($trackObj['restrictions'])
            ? new RestrictionsObject($trackObj['restrictions']['reason'])
            : null;
        return new TrackObjectFullEntity(
            id: $trackObj['id'],
            album: $albumObjSimplified,
            artists: $artistObjSimplified,
            discNumber: $trackObj['disc_number'],
            durationMs: $trackObj['duration_ms'],
            explicit: $trackObj['explicit'],
            externalIds: $trackObj['external_ids'],
            externalUrls: $trackObj['external_urls'],
            href: $trackObj['href'],
            name: $trackObj['name'],
            popularity: $trackObj['popularity'],
            trackNumber: $trackObj['track_number'],
            uri: $trackObj['uri'],
            availableMarkets: $trackObj['available_markets'] ?? null,
            isLocal: $trackObj['is_local'] ?? false,
            isPlayable: $trackObj['is_playable'] ?? false,
            linkedFrom: $trackObj['linked_from'] ?? null,
            previewUrl: $trackObj['preview_url'] ?? null,
            restrictions: $restrictionObj ?? null,
        );
    }
}
