<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\Entities\SpotifyApi\TrackObjectFullEntity;
use App\Domain\Entities\SpotifyApiCustomResponse\TrackObjectSimplifiedCustom;
use App\Domain\SpotifyApi\TrackRepository;
use App\Infrastructure\Persistence\GeoIP\GeoIPDetectorInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GetSpotifyTrackService
{
    protected SpotifyClientTokenFetchingService $tokenFetchingService;
    protected TrackRepository $trackRepository;
    protected GeoIPDetectorInterface $iPDetector;

    public function __construct(
        SpotifyClientTokenFetchingService $tokenFetchingService,
        TrackRepository                   $trackRepository,
        GeoIPDetectorInterface            $iPDetector,
    )
    {
        $this->tokenFetchingService = $tokenFetchingService;
        $this->trackRepository = $trackRepository;
        $this->iPDetector = $iPDetector;
    }

    public function getTrack(Request $request, Response $response, array $args): Response
    {
        $trackId = $args['id'];
        $acceptLanguage = $request->getHeader('Accept-Language');

        $tokenOrErrorRes = $this->tokenFetchingService->fetch(
            $_ENV['SPOTIFY_CLIENT_ID'],
            $_ENV['SPOTIFY_CLIENT_SECRET'],
        );

        if ($tokenOrErrorRes == null) {
            return (new ErrorResponse(500, 'internal error'))->writeErrorResponse($response);
        }

        $token = $tokenOrErrorRes;

        $trackPagingObj = $this->getTrackInternal(
            $trackId,
            $token->getAccessToken(),
            $acceptLanguage[0] ?? null,
        );

        if ($trackPagingObj instanceof ErrorResponse) {
            return $trackPagingObj->writeErrorResponse($response);
        }

        $jsonBodyToWrite = TrackObjectSimplifiedCustom::fromTrackObjectFull($trackPagingObj)->toJson();

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($jsonBodyToWrite);

        return $response;
    }

    /**
     * @param string $trackId
     * @param string $token
     * @param ?string $acceptLanguage
     * @return ?TrackObjectFullEntity|ErrorResponse
     */
    private function getTrackInternal(
        string  $trackId,
        string  $token,
        ?string $acceptLanguage = null,
    ): TrackObjectFullEntity|ErrorResponse
    {
        $isoCode = $this->iPDetector->detectCountry('JP');

        return $this->trackRepository->getTrackInfo($trackId, $token, $isoCode, $acceptLanguage);
    }
}
