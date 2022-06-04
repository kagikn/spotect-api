<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;

interface AudioFeatureCacheRepository
{
    /**
     * @param  AudioFeaturesObject  $audioFeaturesObject
     * @param  int  $expireForIfAdded
     *
     * @return bool
     */
    public function store(
        AudioFeaturesObject $audioFeaturesObject,
        int $expireForIfAdded = 604800
    ): bool;

    public function get(
        string $id
    ): ?AudioFeaturesObject;
}
