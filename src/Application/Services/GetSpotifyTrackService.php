<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Exception\SpotifyApi\BadRequestParameterException;
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
        TrackRepository $trackRepository,
        GeoIPDetectorInterface $iPDetector,
    ) {
        $this->tokenFetchingService = $tokenFetchingService;
        $this->trackRepository = $trackRepository;
        $this->iPDetector = $iPDetector;
    }

    public function getTrack(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $trackId = $args['id'];
        $acceptLanguage = $request->getHeader('Accept-Language');

        $tokenOrErrorRes = $this->tokenFetchingService->fetch(
            $_ENV['SPOTIFY_CLIENT_ID'],
            $_ENV['SPOTIFY_CLIENT_SECRET'],
        );

        $token = $tokenOrErrorRes;

        try {
            $trackPagingObj = $this->getSimplifiedTrackObject(
                $trackId,
                $token->getAccessToken(),
                $acceptLanguage[0] ?? null,
            );
            $jsonBody = $trackPagingObj->toJson();
        } catch (BadRequestParameterException $exception) {
            $jsonBody = $exception->getJsonStringOfErrorObject();
            $response = $response->withStatus(400);
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($jsonBody);
        return $response;
    }

    private function getSimplifiedTrackObject(
        string $trackId,
        string $accessToken,
        ?string $acceptLanguage,
    ): TrackObjectSimplifiedCustom {
        $trackPagingObj = $this->getTrackInternal(
            $trackId,
            $accessToken,
            $acceptLanguage[0] ?? null,
        );
        return TrackObjectSimplifiedCustom::fromTrackObjectFull(
            $trackPagingObj
        );
    }

    /**
     * @param  string  $trackId
     * @param  string  $token
     * @param ?string  $acceptLanguage
     *
     * @return TrackObjectFullEntity
     */
    private function getTrackInternal(
        string $trackId,
        string $token,
        ?string $acceptLanguage = null,
    ): TrackObjectFullEntity {
        $isoCode = $this->iPDetector->detectCountry('JP');

        return $this->trackRepository->getTrackInfo(
            $trackId,
            $token,
            $isoCode,
            $acceptLanguage
        );
    }
}
