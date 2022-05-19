<?php

namespace App\Domain\Entities\SpotifyApiCustomResponse;

use App\Domain\Entities\SpotifyApi\ImageObject;

class AlbumOfTrackObject
{
    /**
     * @param string $id
     * @param ImageObject[] $coverArt
     * @param string $name
     */
    public function __construct(
        public readonly string $id,
        public readonly array $coverArt,
        public readonly string $name,
    )
    {
    }

    public function toAssociativeArray()
    {
        return [
            'coverArt' => ['sources' => $this->coverArt],
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
