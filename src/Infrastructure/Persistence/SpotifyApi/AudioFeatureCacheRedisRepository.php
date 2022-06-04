<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use Redis;

class AudioFeatureCacheRedisRepository implements AudioFeatureCacheRepository
{
    private const SPOTIFY_AUDIO_FEATURE_KEY_NAME = 'spotify:audio-feature:';

    /**
     * @param  AudioFeaturesObject  $audioFeaturesObject
     * @param  int  $expireForIfAdded
     *
     * @return bool
     */
    public function store(
        AudioFeaturesObject $audioFeaturesObject,
        int $expireForIfAdded = 604800
    ): bool {
        $redis = new Redis();
        if (
            !$redis->pconnect(
                $_ENV['REDIS_HOST'],
                intval($_ENV['REDIS_PORT']),
                floatval($_ENV['REDIS_TIMEOUT'])
            )
        ) {
            return false;
        }

        $baseKey = self::SPOTIFY_AUDIO_FEATURE_KEY_NAME
            . $audioFeaturesObject->id;

        if ($redis->exists($baseKey)) {
            return false;
        }

        $redis->multi();
        $redis->hSet(
            $baseKey,
            'values',
            $audioFeaturesObject->valuesToJson(),
        );
        $redis->expireAt($baseKey, time() + $expireForIfAdded);
        $redis->exec();

        return true;
    }

    public function get(string $id): ?AudioFeaturesObject
    {
        $redis = new Redis();
        if (
            !$redis->pconnect(
                $_ENV['REDIS_HOST'],
                intval($_ENV['REDIS_PORT']),
                floatval($_ENV['REDIS_TIMEOUT'])
            )
        ) {
            return null;
        }

        $baseKey = self::SPOTIFY_AUDIO_FEATURE_KEY_NAME . $id;
        $jsonStrOrFalse = $redis->hGet($baseKey, 'values');

        if (!$jsonStrOrFalse) {
            return null;
        }

        return AudioFeaturesObject::fromValueJsonAndId($id, $jsonStrOrFalse);
    }
}
