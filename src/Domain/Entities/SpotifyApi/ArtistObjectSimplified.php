<?php

namespace App\Domain\Entities\SpotifyApi;

class ArtistObjectSimplified extends ContextObject
{
    /**
     * @param  string  $id
     * @param  array  $externalUrls
     * @param  string  $href
     * @param  string  $name
     * @param  string  $type
     * @param  string  $uri
     */
    public function __construct(
        public readonly string $id,
        array $externalUrls,
        string $href,
        public readonly string $name,
        string $type,
        string $uri,
    ) {
        parent::__construct($externalUrls, $href, $type, $uri);
    }

    /**
     * @param  array  $artists
     *
     * @return ArtistObjectSimplified[]
     */
    public static function fromItemCollectionArray(array $artists): array
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
}
