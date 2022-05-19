<?php

namespace App\Domain\Entities\SpotifyApiCustomResponse;

class PagingInfo
{
    public function __construct(
        public readonly int $limit,
        public readonly ?int $nextOffset,
    ) {
    }
}
