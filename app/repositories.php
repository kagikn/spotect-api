<?php

declare(strict_types=1);

use App\Domain\SpotifyApi\AudioFeatureRepository;
use App\Domain\SpotifyApi\SearchRepository;
use App\Domain\SpotifyApi\TrackRepository;
use App\Domain\SpotifyCredentials\ISpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyCredentialsRepository;
use App\Infrastructure\Persistence\SpotifyApi\AudioFeatureApiRepository;
use App\Infrastructure\Persistence\SpotifyApi\AudioFeatureCacheRedisRepository;
use App\Infrastructure\Persistence\SpotifyApi\AudioFeatureCacheRepository;
use App\Infrastructure\Persistence\SpotifyApi\SearchApiRepository;
use App\Infrastructure\Persistence\SpotifyApi\TrackApiRepository;
use App\Infrastructure\Persistence\SpotifyCredentials\SqliteSpotifyClientCredentialsRepository;
use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        SearchRepository::class => autowire(SearchApiRepository::class),
        TrackRepository::class => autowire(TrackApiRepository::class),
        AudioFeatureRepository::class => autowire(AudioFeatureApiRepository::class),
        AudioFeatureCacheRepository::class => autowire(AudioFeatureCacheRedisRepository::class),
        ISpotifyAuthApi::class => autowire(SpotifyAuthApi::class),
        SpotifyCredentialsRepository::class => autowire(SqliteSpotifyClientCredentialsRepository::class),
    ]);
};
