<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\SpotifyApi\AudioFeaturesObject;
use App\Domain\SpotifyApi\AudioFeatureRepository;
use App\Exception\SpotifyApi\BadRequestParameterException;
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
            $credentialOrErrorRes = $this->tokenFetchingService->fetch(
                $_ENV['SPOTIFY_CLIENT_ID'],
                $_ENV['SPOTIFY_CLIENT_SECRET'],
            );

            try {
                $newAudioFeaturesObj = $this->fetchTrackAudioFeatureInternal(
                    $trackId,
                    $credentialOrErrorRes->getAccessToken(),
                );
            } catch (BadRequestParameterException) {
                $response = $response->withStatus(400);
                $response = $response->withHeader(
                    'Content-Type',
                    'application/json'
                );
                $arrayToWriteAsJsonBody = [
                    'error' => [
                        'status' => 400,
                        'message' => 'invalid id'
                    ]
                ];
                $response->getBody()->write(
                    json_encode($arrayToWriteAsJsonBody),
                );

                return $response;
            }


            $this->audioFeatureCacheRepository->store($newAudioFeaturesObj);
            $audioFeaturesObj = $newAudioFeaturesObj;
        }
        $jsonBody = $audioFeaturesObj->mainValuesToJson();
        $response = $response->withHeader(
            'Content-Type',
            'application/json'
        );
        $response->getBody()->write(
            $jsonBody,
        );

        return $response;
    }

    /**
     * @param  string  $trackId
     * @param  string  $accessToken
     *
     * @return AudioFeaturesObject
     */
    private function fetchTrackAudioFeatureInternal(
        string $trackId,
        string $accessToken,
    ): AudioFeaturesObject {
        return $this->audioFeatureRepository->getAudioFeature(
            $trackId,
            $accessToken
        );
    }
}
