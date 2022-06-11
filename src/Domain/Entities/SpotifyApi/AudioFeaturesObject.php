<?php

namespace App\Domain\Entities\SpotifyApi;

class AudioFeaturesObject
{
    /**
     * @param  string  $id
     * @param  float  $acousticness
     * @param  string  $analysisUrl
     * @param  float  $danceability
     * @param  int  $durationMs
     * @param  float  $energy
     * @param  float  $instrumentalness
     * @param  int  $key
     * @param  float  $liveness
     * @param  float  $loudness
     * @param  int  $mode
     * @param  float  $speechiness
     * @param  float  $tempo
     * @param  int  $timeSignature
     * @param  string  $trackHref
     * @param  string  $type
     * @param  string  $uri
     * @param  float  $valence
     */
    public function __construct(
        public readonly string $id,
        public readonly float $acousticness,
        public readonly string $analysisUrl,
        public readonly float $danceability,
        public readonly int $durationMs,
        public readonly float $energy,
        public readonly float $instrumentalness,
        public readonly int $key,
        public readonly float $liveness,
        public readonly float $loudness,
        public readonly int $mode,
        public readonly float $speechiness,
        public readonly float $tempo,
        public readonly int $timeSignature,
        public readonly string $trackHref,
        public readonly string $type,
        public readonly string $uri,
        public readonly float $valence
    ) {
    }

    public static function fromValueJsonAndId(
        string $id,
        string $valueJson
    ): AudioFeaturesObject {
        $audioFeatureValueArrayAssoc = json_decode(
            $valueJson,
            true,
            512,
            JSON_BIGINT_AS_STRING
        );

        return new AudioFeaturesObject(
            $id,
            $audioFeatureValueArrayAssoc['acousticness'],
            $audioFeatureValueArrayAssoc['analysis_url'],
            $audioFeatureValueArrayAssoc['danceability'],
            $audioFeatureValueArrayAssoc['duration_ms'],
            $audioFeatureValueArrayAssoc['energy'],
            $audioFeatureValueArrayAssoc['instrumentalness'],
            $audioFeatureValueArrayAssoc['key'],
            $audioFeatureValueArrayAssoc['liveness'],
            $audioFeatureValueArrayAssoc['loudness'],
            $audioFeatureValueArrayAssoc['mode'],
            $audioFeatureValueArrayAssoc['speechiness'],
            $audioFeatureValueArrayAssoc['tempo'],
            $audioFeatureValueArrayAssoc['time_signature'],
            $audioFeatureValueArrayAssoc['track_href'],
            $audioFeatureValueArrayAssoc['type'],
            $audioFeatureValueArrayAssoc['uri'],
            $audioFeatureValueArrayAssoc['valence'],
        );
    }

    public static function fromJson(string $itemArray): AudioFeaturesObject
    {
        $audioFeatureArrayAssoc = json_decode(
            $itemArray,
            true,
        );

        return self::fromItemArray($audioFeatureArrayAssoc);
    }

    public static function fromItemArray(array $item): AudioFeaturesObject
    {
        return new AudioFeaturesObject(
            $item['id'],
            $item['acousticness'],
            $item['analysis_url'],
            $item['danceability'],
            $item['duration_ms'],
            $item['energy'],
            $item['instrumentalness'],
            $item['key'],
            $item['liveness'],
            $item['loudness'],
            $item['mode'],
            $item['speechiness'],
            $item['tempo'],
            $item['time_signature'],
            $item['track_href'],
            $item['type'],
            $item['uri'],
            $item['valence'],
        );
    }

    public function mainValuesToJson(): string
    {
        $array = [
            'acousticness' => $this->acousticness,
            'danceability' => $this->danceability,
            'duration_ms' => $this->durationMs,
            'energy' => $this->energy,
            'instrumentalness' => $this->instrumentalness,
            'key' => $this->key,
            'liveness' => $this->liveness,
            'loudness' => $this->loudness,
            'mode' => $this->mode,
            'speechiness' => $this->speechiness,
            'tempo' => $this->tempo,
            'time_signature' => $this->timeSignature,
            'valence' => $this->valence,
        ];

        return json_encode($array, JSON_PRESERVE_ZERO_FRACTION);
    }

    public function valuesToJson(): string
    {
        $array = [
            'acousticness' => $this->acousticness,
            'analysis_url' => $this->analysisUrl,
            'danceability' => $this->danceability,
            'duration_ms' => $this->durationMs,
            'energy' => $this->energy,
            'instrumentalness' => $this->instrumentalness,
            'key' => $this->key,
            'liveness' => $this->liveness,
            'loudness' => $this->loudness,
            'mode' => $this->mode,
            'speechiness' => $this->speechiness,
            'tempo' => $this->tempo,
            'time_signature' => $this->timeSignature,
            'track_href' => $this->trackHref,
            'type' => $this->type,
            'uri' => $this->uri,
            'valence' => $this->valence,
        ];

        return json_encode($array, JSON_PRESERVE_ZERO_FRACTION);
    }
}
