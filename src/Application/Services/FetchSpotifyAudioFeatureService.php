<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Domain\Entities\SpotifyApi\ErrorResponse;
use App\Domain\SpotifyApi\AudioFeatureRepository;
use App\Infrastructure\Persistence\SpotifyApi\AudioFeatureCacheRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FetchSpotifyAudioFeatureService
{
    protected SpotifyClientTokenFetchingService $tokenFetchingService;
    protected AudioFeatureRepository $audioFeatureRepository;
    protected AudioFeatureCacheRepository $audioFeatureCacheRepository;

    public function __construct(
        SpotifyClientTokenFetchingService $tokenFetchingService,
        AudioFeatureRepository $audioFeatureRepository,
        AudioFeatureCacheRepository $audioFeatureCacheRepository
    ) {
        $this->tokenFetchingService = $tokenFetchingService;
        $this->audioFeatureRepository = $audioFeatureRepository;
        $this->audioFeatureCacheRepository = $audioFeatureCacheRepository;
    }

    public function fetchTrackAudioFeature(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $trackId = $args['id'];

        $audioFeaturesObj = $this->audioFeatureCacheRepository->get($trackId);
        if (!isset($audioFeaturesObj)) {
            $tokenOrErrorRes = $this->tokenFetchingService->fetch(
                $_ENV['SPOTIFY_CLIENT_ID'],
                $_ENV['SPOTIFY_CLIENT_SECRET'],
            );

            if ($tokenOrErrorRes == null) {
                return (new ErrorResponse(
                    500,
                    'internal error'
                ))->writeErrorResponse($response);
            }

            $token = $tokenOrErrorRes;

            $audioFeaturesObjOrError = $this->fetchTrackAudioFeatureInternal(
                $args['id'],
                $token->getAccessToken(),
            );

            if ($audioFeaturesObjOrError instanceof ErrorResponse) {
                return $audioFeaturesObjOrError->writeErrorResponse($response);
            }

            $this->audioFeatureCacheRepository->store($audioFeaturesObjOrError);
            $audioFeaturesObj = $audioFeaturesObjOrError;
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($audioFeaturesObj->mainValuesToJson());

        return $response;
    }

    /**
     * @param  string  $trackId
     * @param  string  $accessToken
     *
     * @return AudioFeaturesObject|ErrorResponse
     */
    private function fetchTrackAudioFeatureInternal(
        string $trackId,
        string $accessToken,
    ): AudioFeaturesObject|ErrorResponse {
        return $this->audioFeatureRepository->getAudioFeature(
            $trackId,
            $accessToken
        );
    }
}
