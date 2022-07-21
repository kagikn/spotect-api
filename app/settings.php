<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => $_ENV['APP_DEBUG'],
                'logError' => true,
                'logErrorDetails' => true,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout'
                        : __DIR__ . '/../logs/app.log',
                    'level' => $_ENV['APP_DEBUG'] ? Logger::DEBUG
                        : Logger::WARNING,
                ],
                'GeoIp' => [
                    'filepath' => __DIR__ . '/../resource/GeoLite2-Country.mmdb'
                ]
            ]);
        }
    ]);
};
