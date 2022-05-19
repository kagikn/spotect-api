<?php

declare(strict_types=1);

use App\Application\Middleware\NoXPoweredByMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\App;

return function (App $app) {
    $contentLengthMiddleware = new ContentLengthMiddleware();
    $app->add($contentLengthMiddleware);
    $app->add(NoXPoweredByMiddleware::class);
    $app->add(SessionMiddleware::class);
};
