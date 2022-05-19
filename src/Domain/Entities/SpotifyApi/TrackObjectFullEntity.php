<?php

namespace App\Domain\Entities\SpotifyApi;

class TrackObjectFullEntity extends TrackObjectSimplified
{
    public function __construct(
        string             $id,
        public             readonly AlbumObjectSimplified $album,
        array              $artists,
        int                $discNumber,
        int                $durationMs,
        bool               $explicit,
        public             readonly array $externalIds,
        array              $externalUrls,
        string             $href,
        string             $name,
        public             readonly int $popularity,
        int                $trackNumber,
        string             $uri,
        array              $availableMarkets = null,
        public             readonly bool $isLocal = false,
        bool               $isPlayable = false,
        TrackLinkObject    $linkedFrom = null,
        string             $previewUrl = null,
        RestrictionsObject $restrictions = null,
    )
    {
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
     * @param array $items
     * @return TrackObjectFullEntity[]
     */
    public static function fromItemsArrayOfResponse(array $items): array
    {
        $array_map = [];
        foreach ($items as $key => $item) {
            $albumObjSimplified =
                AlbumObjectSimplified::fromItemArrayOfResponse($item['album']);
            $artistObjSimplifiedArray =
                ArtistObjectSimplified::fromArtistsItemsArrayOfResponse($item['artists']);
            $array_map[$key] = new TrackObjectFullEntity(
                id: $item['id'],
                album: $albumObjSimplified,
                artists: $artistObjSimplifiedArray,
                discNumber: $item['disc_number'],
                durationMs: $item['duration_ms'],
                explicit: $item['explicit'],
                externalIds: $item['external_ids'],
                externalUrls: $item['external_urls'],
                href: $item['href'],
                name: $item['name'],
                popularity: $item['popularity'],
                trackNumber: $item['track_number'],
                uri: $item['uri'],
                availableMarkets: $item['available_markets'] ?? null,
                isLocal: $item['is_local'] ?? false,
                isPlayable: $item['is_playable'] ?? false,
                linkedFrom: $item['linked_from'] ?? null,
                previewUrl: $item['preview_url'] ?? null,
                restrictions: $item['restrictions'] ?? null,
            );
        }
        return $array_map;
    }
}
