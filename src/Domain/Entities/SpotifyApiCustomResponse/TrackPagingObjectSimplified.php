<?php

namespace App\Domain\Entities\SpotifyApiCustomResponse;

use _PHPStan_3e014c27f\Nette\Neon\Exception;
use App\Domain\Entities\SpotifyApi\TrackObjectSimplified;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;

class TrackPagingObjectSimplified
{
    /**
     * @param TrackObjectSimplifiedCustom[] $items
     * @param PagingInfo $pagingInfo
     * @param int $totalCount
     */
    public function __construct(
        public readonly array $items,
        public readonly PagingInfo $pagingInfo,
        public readonly int $totalCount,
    )
    {
    }

    public static function fromTrackPagingObjectFull(TrackPagingObject $trackPagingObject): TrackPagingObjectSimplified
    {
        $trackItems = [];
        foreach ($trackPagingObject->items as $item) {
            $albumData = $item->album;
            $albumOfTrackObj = new AlbumOfTrackObject($albumData->id, $albumData->imageObjs, $albumData->name);

            $artistArray = [];
            foreach ($item->artists as $artist) {
                $artistArray[] = new ArtistObjectMinimum($artist->id, $artist->name);
            }

            $trackItems[] = new TrackObjectSimplifiedCustom(
                $item->id,
                $albumOfTrackObj,
                $artistArray,
                $item->durationMs,
                $item->explicit,
                $item->name,
                $item->isPlayable,
            );
        }

        $pagingInfo = new PagingInfo(
            $trackPagingObject->limit,
            isset($trackPagingObject->next) && count($trackItems) >= $trackPagingObject->limit
                ? $trackPagingObject->offset + $trackPagingObject->limit
                : null
        );

        return new TrackPagingObjectSimplified($trackItems, $pagingInfo, $trackPagingObject->total);
    }

    public function toJson()
    {
        $itemAssocArray = array_map(fn($item) => $item->toAssociativeArray(), $this->items);

        $array = [
            'items' => $itemAssocArray,
            'pagingInfo' => [
                'nextOffset' => $this->pagingInfo->nextOffset,
                'limit' => $this->pagingInfo->limit
            ],
            'totalCount' => $this->totalCount,
        ];

        return json_encode($array, JSON_PRESERVE_ZERO_FRACTION);
    }
}
