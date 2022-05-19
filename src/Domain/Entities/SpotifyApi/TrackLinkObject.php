<?php

namespace App\Domain\Entities\SpotifyApi;

class TrackLinkObject
{
    public readonly array $externalUrls;
    public readonly string $href;
    public readonly string $id;
    public readonly string $uri;

    public function __construct(
        string $id,
        array $externalUrls,
        string $href,
        string $uri,
    ) {
        $this->externalUrls = $externalUrls;
        $this->href = $href;
        $this->id = $id;
        $this->uri = $uri;
    }
}
