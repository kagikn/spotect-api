<?php

declare(strict_types=1);

use App\Application\Services\FetchSpotifyAudioFeatureService;
use App\Application\Services\GetSpotifyTrackService;
use App\Application\Services\SearchSpotifyService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $text = var_export($request->getHeader('Accept-Language'), true);
        $response->getBody()->write($text);
        return $response;
    });

    $app->group('/v1', function (Group $group) {
        $group->get('/search', [SearchSpotifyService::class, 'search']);
        $group->get('/audio-features/{id}', [FetchSpotifyAudioFeatureService::class, 'fetchTrackAudioFeature']);
        $group->get('/tracks/{id}', [GetSpotifyTrackService::class, 'getTrack']);
    });
};