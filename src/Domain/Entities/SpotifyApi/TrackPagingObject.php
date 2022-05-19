<?php

namespace App\Domain\Entities\SpotifyApi;

class TrackPagingObject
{
    /**
     * @param string $href
     * @param TrackObjectFullEntity[] $items
     * @param int $limit
     * @param int $offset
     * @param int $total
     * @param ?string $next
     * @param ?string $previous
     */
    public function __construct(
        public readonly string $href,
        public readonly array $items,
        public readonly int $limit,
        public readonly int $offset,
        public readonly int $total,
        public readonly ?string $next = null,
        public readonly ?string $previous = null,
    )
    {
    }

    /**
     * @param array $trackPagingInfoJson
     * @return ?TrackPagingObject
     */
    public static function fromTrackSearchResponse(array $trackPagingInfoJson): ?TrackPagingObject
    {
        return new TrackPagingObject(
            href: $trackPagingInfoJson['href'],
            items: TrackObjectFullEntity::fromItemsArrayOfResponse($trackPagingInfoJson['items']),
            limit: $trackPagingInfoJson['limit'],
            offset: $trackPagingInfoJson['offset'],
            total: $trackPagingInfoJson['total'],
            next: $trackPagingInfoJson['next'] ?? null,
            previous: $trackPagingInfoJson['previous'] ?? null,
        );
    }
}
