<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyApi;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\SearchResponseParser;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\SpotifyApi\SearchRepository;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class SearchApiRepository implements SearchRepository
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    /**
     * @param array $queryParams
     * @param string $accessToken
     * @param ?string $acceptLanguageHeader
     * @return TrackPagingObject|ErrorResponse
     * @throws GuzzleException
     */
    public function searchForTrack(
        array  $queryParams,
        string $accessToken,
        string $acceptLanguageHeader = null
    ): TrackPagingObject|ErrorResponse
    {
        $res = $this->client->get('search', $accessToken, $queryParams, $acceptLanguageHeader);

        $jsonBody = $res->getBody()->getContents();
        $statusCode = $res->getStatusCode();

        if ($statusCode != 200) {
            $jsonArray = json_decode($jsonBody, true);
            $error = $jsonArray['error'];
            return new ErrorResponse($statusCode, $error['message']);
        }

        $searchResultJson = json_decode($jsonBody, true);

        return TrackPagingObject::fromTrackSearchResponse($searchResultJson['tracks']);
    }
}
