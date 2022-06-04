<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;

interface AudioFeatureCacheRepository
{
    /**
     * @param  AudioFeaturesObject  $audioFeaturesObject
     * @param  int  $expireFor
     *
     * @return bool
     */
    public function store(
        AudioFeaturesObject $audioFeaturesObject,
        int $expireFor = 604800
    ): bool;

    public function get(
        string $id,
        int $ExpireForMax = 2592000
    ): ?AudioFeaturesObject;
}
