<?php

declare(strict_types=1);

use App\Application\Services\GetSpotifyTrackService;
use App\Application\Services\SearchSpotifyService;
use App\Application\Settings\SettingsInterface;
use App\Application\Services\SpotifyClientTokenFetchingService;
use App\Domain\SpotifyApi\SearchRepository;
use App\Domain\SpotifyApi\TrackRepository;
use App\Domain\SpotifyCredentials\ISpotifyAuthApi;
use App\Domain\SpotifyCredentials\SpotifyCredentialsRepository;
use App\Infrastructure\Persistence\GeoIP\GeoIPDetectorInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Infrastructure\Persistence\GeoIP\GeoIPDetector;
use GeoIp2\Database\Reader;

use function DI\create;
use function DI\get;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        GeoIPDetectorInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $geoIPDDatabaseFilepath = $settings->get('GeoIp')['filepath'];
            $reader = new Reader($geoIPDDatabaseFilepath);

            return new GeoIPDetector($reader);
        },
        SpotifyClientTokenFetchingService::class => create()
            ->constructor(
                get(ISpotifyAuthApi::class),
                get(SpotifyCredentialsRepository::class),
                get(LoggerInterface::class)
            ),
        SearchSpotifyService::class => create()
            ->constructor(
                get(SpotifyClientTokenFetchingService::class),
                get(SearchRepository::class),
                get(GeoIPDetectorInterface::class)
            ),
        GetSpotifyTrackService::class => create()
            ->constructor(
                get(SpotifyClientTokenFetchingService::class),
                get(TrackRepository::class),
                get(GeoIPDetectorInterface::class)
            ),
    ]);
};
