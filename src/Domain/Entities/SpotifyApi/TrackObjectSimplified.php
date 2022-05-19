<?php

namespace App\Domain\Entities\SpotifyApi;

class TrackObjectSimplified
{
    /**
     * @param string $id
     * @param ArtistObjectSimplified[] $artists
     * @param int $discNumber
     * @param int $durationMs
     * @param bool $explicit
     * @param array $externalUrls
     * @param string $href
     * @param string $name
     * @param int $trackNumber
     * @param string $uri
     * @param string[]|null $availableMarkets
     * @param bool $isPlayable
     * @param TrackLinkObject|null $linkedFrom
     * @param string|null $previewUrl
     * @param RestrictionsObject|null $restrictions
     */
    public function __construct(
        public readonly string $id,
        public readonly array $artists,
        public readonly int $discNumber,
        public readonly int $durationMs,
        public readonly bool $explicit,
        public readonly array $externalUrls,
        public readonly string $href,
        public readonly string $name,
        public readonly int $trackNumber,
        public readonly string $uri,
        public readonly ?array $availableMarkets = null,
        public readonly bool $isPlayable = false,
        public readonly ?TrackLinkObject $linkedFrom = null,
        public readonly ?string $previewUrl = null,
        public readonly ?RestrictionsObject $restrictions = null,
    )
    {
    }
}