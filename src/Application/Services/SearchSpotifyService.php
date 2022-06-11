<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackPagingObjectSimplified;
use App\Domain\SpotifyApi\SearchRepository;
use App\Exception\SpotifyApi\BadRequestParameterException;
use App\Exception\SpotifyApi\SpotifyApiException;
use App\Infrastructure\Persistence\GeoIP\GeoIPDetectorInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SearchSpotifyService
{
    protected SpotifyClientTokenFetchingService $tokenFetchingService;
    protected SearchRepository $searchRepository;
    protected GeoIPDetectorInterface $iPDetector;

    public function __construct(
        SpotifyClientTokenFetchingService $tokenFetchingService,
        SearchRepository $searchRepository,
        GeoIPDetectorInterface $iPDetector,
    ) {
        $this->tokenFetchingService = $tokenFetchingService;
        $this->searchRepository = $searchRepository;
        $this->iPDetector = $iPDetector;
    }

    public function search(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $queryParams = $request->getQueryParams();
        $acceptLanguage = $request->getHeader('Accept-Language');

        $token = $this->tokenFetchingService->fetch(
            $_ENV['SPOTIFY_CLIENT_ID'],
            $_ENV['SPOTIFY_CLIENT_SECRET'],
        );

        try {
            $trackPagingObj = $this->searchInternal(
                $queryParams,
                $token->getAccessToken(),
                $acceptLanguage[0] ?? '',
            );

            $trackPagingObjSimplified = TrackPagingObjectSimplified::fromTrackPagingObjectFull(
                $trackPagingObj
            );
            $jsonBody = $trackPagingObjSimplified->toJson();
        } catch (BadRequestParameterException $exception) {
            $jsonBody = $exception->getJsonStringOfErrorObject();
            $response = $response->withStatus(400);
        }

        $response = $response->withHeader(
            'Content-Type',
            'application/json'
        );
        $response->getBody()->write($jsonBody);

        return $response;
    }

    /**
     * @param  array  $queries
     * @param  string  $token
     * @param ?string  $acceptLanguage
     *
     * @return TrackPagingObject
     * @throws SpotifyApiException
     * @throws GuzzleException
     */
    private function searchInternal(
        array $queries,
        string $token,
        ?string $acceptLanguage = null,
    ): TrackPagingObject {
        if (!isset($queries['market'])) {
            $queries['market'] = $this->iPDetector->detectCountry('JP');
        }

        return $this->searchRepository->searchForTrack(
            $queries,
            $token,
            $acceptLanguage
        );
    }
}
