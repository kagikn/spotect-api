<?php

namespace App\Domain\Entities\SpotifyApi;

class AlbumObjectSimplified extends ContextObject
{
    /**
     * @param string $id
     * @param string $albumType
     * @param array $artists
     * @param array $externalUrls
     * @param string $href
     * @param ImageObject[] $imageObjs
     * @param string $name
     * @param string $releaseDate
     * @param string $releaseDatePrecision
     * @param string $totalTracks
     * @param string $type
     * @param string $uri
     * @param string|null $albumGroup
     * @param string[]|null $availableMarkets
     * @param RestrictionsObject|null $restrictions
     */
    public function __construct(
        public readonly string $id,
        public readonly string $albumType,
        public readonly array $artists,
        array  $externalUrls,
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
    )
    {
        parent::__construct($externalUrls, $href, $type, $uri);
    }

    /**
     * @param array $albumInfo
     * @return AlbumObjectSimplified
     */
    public static function fromItemArrayOfResponse(array $albumInfo): AlbumObjectSimplified
    {
        $artistObjSimplifiedArray = ArtistObjectSimplified::fromArtistsItemsArrayOfResponse($albumInfo['artists']);
        $imageObjSimplifiedArray = ImageObject::fromImageObjectItemsArrayOfResponse($albumInfo['images']);

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
}
