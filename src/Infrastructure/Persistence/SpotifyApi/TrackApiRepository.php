<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\SpotifyApi\TrackRepository;

class TrackApiRepository implements TrackRepository
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    /**
     * @param string $trackId
     * @param string $accessToken
     * @param ?string $market
     * @param ?string $acceptLanguageHeader
     * @return TrackObjectFullEntity|ErrorResponse
     */
    public function getTrackInfo(
        string $trackId,
        string $accessToken,
        string $market = null,
        string $acceptLanguageHeader = null
    ): TrackObjectFullEntity|ErrorResponse
    {
        $res = $this->client->get(
            'tracks/' . $trackId,
            $accessToken,
            $market,
            $acceptLanguageHeader,
        );

        $jsonArray = json_decode($res->getBody()->getContents(), true);
        $statusCode = $res->getStatusCode();

        if ($statusCode != 200) {
            $error = $jsonArray['error'];
            return new ErrorResponse($error['status'], $error['message']);
        }

        $searchResultJson = $jsonArray;

        return TrackObjectFullEntity::fromTrackObjItemArray($searchResultJson);
    }
}
