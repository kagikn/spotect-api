<?php

namespace App\Domain\Entities\SpotifyApi;

class TrackPagingObject
{
    /**
     * @param  string  $href
     * @param  TrackObjectFullEntity[]  $items
     * @param  int  $limit
     * @param  int  $offset
     * @param  int  $total
     * @param ?string  $next
     * @param ?string  $previous
     */
    public function __construct(
        public readonly string $href,
        public readonly array $items,
        public readonly int $limit,
        public readonly int $offset,
        public readonly int $total,
        public readonly ?string $next = null,
        public readonly ?string $previous = null,
    ) {
    }

    /**
     * @param  array  $itemCollection
     *
     * @return ?TrackPagingObject
     */
    public static function fromTrackSearchResponse(
        array $itemCollection
    ): ?TrackPagingObject {
        return new TrackPagingObject(
            href: $itemCollection['href'],
            items: TrackObjectFullEntity::fromItemsArrayOfResponse(
                $itemCollection['items']
            ),
            limit: $itemCollection['limit'],
            offset: $itemCollection['offset'],
            total: $itemCollection['total'],
            next: $itemCollection['next'] ?? null,
            previous: $itemCollection['previous'] ?? null,
        );
    }
}
