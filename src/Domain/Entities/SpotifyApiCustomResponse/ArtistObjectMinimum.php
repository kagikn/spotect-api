<?php

namespace App\Domain\Entities\SpotifyApiCustomResponse;

class ArtistObjectMinimum
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    public function toAssociativeArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
