<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\TrackPagingObject;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackPagingObjectSimplified;
use App\Domain\SpotifyApi\SearchRepository;
use App\Infrastructure\Persistence\GeoIP\GeoIPDetectorInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SearchSpotifyService
{
    protected SpotifyClientTokenFetchingService $tokenFetchingService;
    protected SearchRepository $searchRepository;
    protected GeoIPDetectorInterface $iPDetector;

    public function __construct(
        SpotifyClientTokenFetchingService $tokenFetchingService,
        SearchRepository                  $searchRepository,
        GeoIPDetectorInterface            $iPDetector,
    )
    {
        $this->tokenFetchingService = $tokenFetchingService;
        $this->searchRepository = $searchRepository;
        $this->iPDetector = $iPDetector;
    }

    public function search(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getQueryParams();
        $acceptLanguage = $request->getHeader('Accept-Language');

        $tokenOrErrorRes = $this->tokenFetchingService->fetch(
            $_ENV['SPOTIFY_CLIENT_ID'],
            $_ENV['SPOTIFY_CLIENT_SECRET'],
        );

        if ($tokenOrErrorRes == null) {
            return (new ErrorResponse(500, 'internal error'))->writeErrorResponse($response);
        }

        $token = $tokenOrErrorRes;

        $trackPagingObjOrError = $this->searchInternal(
            $queryParams,
            $token->getAccessToken(),
            $acceptLanguage[0] ?? '',
        );


        if ($trackPagingObjOrError instanceof ErrorResponse) {
            return $trackPagingObjOrError->writeErrorResponse($response);
        }


        $jsonBodyToWrite = TrackPagingObjectSimplified::fromTrackPagingObjectFull($trackPagingObjOrError)->toJson();

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($jsonBodyToWrite);

        return $response;
    }

    /**
     * @param array $queries
     * @param string $token
     * @param ?string $acceptLanguage
     * @return TrackPagingObject|ErrorResponse
     */
    private function searchInternal(
        array   $queries,
        string  $token,
        ?string $acceptLanguage = null,
    ): TrackPagingObject|ErrorResponse
    {
        if (!isset($queries['market'])) {
            $queries['market'] = $this->iPDetector->detectCountry('JP');
        }

        return $this->searchRepository->searchForTrack($queries, $token, $acceptLanguage);
    }
}
