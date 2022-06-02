<?php

namespace App\Domain\Entities\SpotifyApi;

class SearchResponseParser
{
    private function __construct()
    {
    }

    /**
     * @param array $responseJson
     * @return ?TrackPagingObject
     */
    public static function parseTrackSearchResponse(array $responseJson): ?TrackPagingObject
    {
        if (!isset($responseJson['tracks'])) {
            return null;
        }

        $trackPagingInfo = $responseJson['tracks'];

        return new TrackPagingObject(
            href: $trackPagingInfo['href'],
            items: self::parseTrackObjectFullArray($trackPagingInfo['items']),
            limit: $trackPagingInfo['limit'],
            offset: $trackPagingInfo['offset'],
            total: $trackPagingInfo['total'],
            next: $trackPagingInfo['next'] ?? null,
            previous: $trackPagingInfo['previous'] ?? null,
        );
    }

    /**
     * @param array $images
     * @return ImageObject[]
     */
    public static function parseImageObjectArray(array $images): array
    {
        return array_map(fn($image) => new ImageObject($image['height'], $image['width'], $image['url']), $images);
    }

    /**
     * @param array $albumInfo
     * @return AlbumObjectSimplified
     */
    public static function parseAlbumObjectSimplified(array $albumInfo): AlbumObjectSimplified
    {
        $artistObjSimplifiedArray = self::parseArtistObjectSimplifiedArray($albumInfo['artists']);
        $imageObjSimplifiedArray = self::parseImageObjectArray($albumInfo['images']);

        $restrictionObj = isset($albumInfo['restrictions'])
            ? new RestrictionsObject($albumInfo['restrictions']['reason'])
            : null;

        return new AlbumObjectSimplified(
            id: $albumInfo['id'],
            albumType: $albumInfo['album_type'],
            artists: $artistObjSimplifiedArray,
            externalUrls: $albumInfo['external_urls'],
            href: $albumInfo['href'],
            imageObjs: $imageObjSimplifiedArray,
            name: $albumInfo['name'],
            releaseDate: $albumInfo['release_date'],
            releaseDatePrecision: $albumInfo['release_date_precision'],
            totalTracks: $albumInfo['total_tracks'],
            type: $albumInfo['type'],
            uri: $albumInfo['uri'],
            albumGroup: $albumInfo['album_group'] ?? null,
            availableMarkets: $albumInfo['available_markets'] ?? null,
            restrictions: $restrictionObj
        );
    }

    /**
     * @param array $artists
     * @return ArtistObjectSimplified[]
     */
    public static function parseArtistObjectSimplifiedArray(array $artists): array
    {
        return array_map(fn($artist) => new ArtistObjectSimplified(
            $artist['id'],
            $artist['external_urls'],
            $artist['href'],
            $artist['name'],
            $artist['type'],
            $artist['uri']
        ), $artists);
    }

    /**
     * @param array $items
     * @return TrackObjectFullEntity[]
     */
    public static function parseTrackObjectFullArray(array $items): array
    {
        $array_map = [];
        foreach ($items as $key => $item) {
            $albumObjSimplified = self::parseAlbumObjectSimplified($item['album']);
            $artistObjSimplifiedArray = self::parseArtistObjectSimplifiedArray($item['artists']);
            $restrictionObj = isset($item['restrictions'])
                ? new RestrictionsObject($item['restrictions']['reason'])
                : null;
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
                restrictions: $restrictionObj ?? null,
            );
        }
        return $array_map;
    }
}
