<?php

namespace App\Domain\Entities\SpotifyApiCustomResponse;

use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;

class TrackObjectSimplifiedCustom
{
    /**
     * @param string $id
     * @param AlbumOfTrackObject $albumOfTrack
     * @param ArtistObjectMinimum[] $artists
     * @param int $duration
     * @param bool $explicit
     * @param string $name
     * @param bool $playable
     */
    public function __construct(
        public readonly string $id,
        public readonly AlbumOfTrackObject $albumOfTrack,
        public readonly array $artists,
        public readonly int $duration,
        public readonly bool $explicit,
        public readonly string $name,
        public readonly bool $playable,
    )
    {
    }

    public static function fromTrackObjectFull(TrackObjectFullEntity $trackObjFull): TrackObjectSimplifiedCustom
    {
        $albumData = $trackObjFull->album;
        $albumOfTrackObj = new AlbumOfTrackObject($albumData->id, $albumData->imageObjs, $albumData->name);

        $artistArray = [];
        foreach ($trackObjFull->artists as $artist) {
            $artistArray[] = new ArtistObjectMinimum($artist->id, $artist->name);
        }

        return new TrackObjectSimplifiedCustom(
            $trackObjFull->id,
            $albumOfTrackObj,
            $artistArray,
            $trackObjFull->durationMs,
            $trackObjFull->explicit,
            $trackObjFull->name,
            $trackObjFull->isPlayable,
        );
    }

    public function toAssociativeArray()
    {
        $artistAssocArray = array_map(fn($artist) => $artist->toAssociativeArray(), $this->artists);
        return [
            'id' => $this->id,
            'albumOfTrack' => $this->albumOfTrack->toAssociativeArray(),
            'artists' => $artistAssocArray,
            'duration' => $this->duration,
            'explicit' => $this->explicit,
            'name' => $this->name,
            'playable' => $this->playable,
        ];
    }

    public function toJson()
    {
        return json_encode(self::toAssociativeArray(), JSON_PRESERVE_ZERO_FRACTION);
    }
}
