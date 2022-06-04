<?php

namespace App\Domain\Entities\SpotifyApi;

class ContextObject
{
    /**
     * @param  array  $externalUrls
     * @param  string  $href
     * @param  string  $type
     * @param  string  $uri
     */
    public function __construct(
        public readonly array $externalUrls,
        public readonly string $href,
        public readonly string $type,
        public readonly string $uri,
    ) {
    }
}
