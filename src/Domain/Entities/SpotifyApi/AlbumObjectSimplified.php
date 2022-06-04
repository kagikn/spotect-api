<?php

namespace App\Domain\Entities\SpotifyApi;

class AlbumObjectSimplified extends ContextObject
{
    /**
     * @param  string  $id
     * @param  string  $albumType
     * @param  array  $artists
     * @param  array  $externalUrls
     * @param  string  $href
     * @param  ImageObject[]  $imageObjs
     * @param  string  $name
     * @param  string  $releaseDate
     * @param  string  $releaseDatePrecision
     * @param  string  $totalTracks
     * @param  string  $type
     * @param  string  $uri
     * @param  string|null  $albumGroup
     * @param  string[]|null  $availableMarkets
     * @param  RestrictionsObject|null  $restrictions
     */
    public function __construct(
        public readonly string $id,
        public readonly string $albumType,
        public readonly array $artists,
        array $externalUrls,
        string $href,
        public readonly array $imageObjs,
        public readonly string $name,
        public readonly string $releaseDate,
        public readonly string $releaseDatePrecision,
        public readonly string $totalTracks,
        string $type,
        string $uri,
        public readonly ?string $albumGroup = null,
        public readonly ?array $availableMarkets = null,
        public readonly ?RestrictionsObject $restrictions = null,
    ) {
        parent::__construct($externalUrls, $href, $type, $uri);
    }

    /**
     * @param  array  $album
     *
     * @return AlbumObjectSimplified
     */
    public static function fromItemArray(array $album): AlbumObjectSimplified
    {
        $artistObjSimplifiedArray
            = ArtistObjectSimplified::fromItemCollectionArray(
            $album['artists']
        );
        $imageObjSimplifiedArray
            = ImageObject::fromImageObjectItemsArrayOfResponse(
            $album['images']
        );

        $restrictionObj = isset($album['restrictions'])
            ? new RestrictionsObject($album['restrictions']['reason'])
            : null;

        return new AlbumObjectSimplified(
            id: $album['id'],
            albumType: $album['album_type'],
            artists: $artistObjSimplifiedArray,
            externalUrls: $album['external_urls'],
            href: $album['href'],
            imageObjs: $imageObjSimplifiedArray,
            name: $album['name'],
            releaseDate: $album['release_date'],
            releaseDatePrecision: $album['release_date_precision'],
            totalTracks: $album['total_tracks'],
            type: $album['type'],
            uri: $album['uri'],
            albumGroup: $album['album_group'] ?? null,
            availableMarkets: $album['available_markets'] ?? null,
            restrictions: $restrictionObj
        );
    }
}
