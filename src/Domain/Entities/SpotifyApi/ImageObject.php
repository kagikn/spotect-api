<?php

namespace App\Domain\Entities\SpotifyApi;

class ImageObject
{
    /**
     * @param int $height
     * @param int $width
     * @param string $url
     */
    public function __construct(
        public readonly int $height,
        public readonly int $width,
        public readonly string $url,
    )
    {
    }

    /**
     * @param array $images
     * @return ImageObject[]
     */
    public static function fromImageObjectItemsArrayOfResponse(array $images): array
    {
        return array_map(fn($image) => new ImageObject($image['height'], $image['width'], $image['url']), $images);
    }
}
