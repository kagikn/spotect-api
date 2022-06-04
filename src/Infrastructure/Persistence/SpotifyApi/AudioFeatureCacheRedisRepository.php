<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use Redis;

class AudioFeatureCacheRedisRepository implements AudioFeatureCacheRepository
{
    private const SPOTIFY_AUDIO_FEATURE_KEY_NAME = 'spotify:audio-feature:';
    private const CREATED_AT_KEY_STR = 'created_at';

    /**
     * @param  AudioFeaturesObject  $audioFeaturesObject
     * @param  int  $expireFor
     *
     * @return bool
     */
    public function store(
        AudioFeaturesObject $audioFeaturesObject,
        int $expireFor = 604800
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
            $redis->multi();
            $redis->hSet(
                $baseKey,
                'values',
                $audioFeaturesObject->valuesToJson(),
            );
        } else {
            $redis->multi();
            $redis->hMSet(
                $baseKey,
                [
                    'values' => $audioFeaturesObject->valuesToJson(),
                    self::CREATED_AT_KEY_STR => strval(time())
                ]
            );
        }
        $redis->expire($baseKey, $expireFor);
        $redis->exec();

        return true;
    }

    public function get(
        string $id,
        int $expireFor = 604800,
        int $expireForMax = 2592000
    ): ?AudioFeaturesObject {
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
        if (!$redis->exists($baseKey)) {
            return null;
        }
        $createdAtVal = intval(
            $redis->hGet($baseKey, self::CREATED_AT_KEY_STR)
        );
        if ($createdAtVal + $expireForMax < time()) {
            $redis->del($baseKey);
            return null;
        }

        $jsonStr = $redis->hGet($baseKey, 'values');
        $redis->expire($baseKey, $expireFor);

        return AudioFeaturesObject::fromValueJsonAndId($id, $jsonStr);
    }
}
