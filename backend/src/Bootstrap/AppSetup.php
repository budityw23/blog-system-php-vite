<?php

namespace App\Bootstrap;

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use App\Middleware\CorsMiddleware;

class AppSetup
{
    public static function configure(App $app)
    {
        // Add middleware
        $app->add(new CorsMiddleware());
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        
        // Add error middleware
        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            true,
            true,
            true
        );
        $app->add($errorMiddleware);

        return $app;
    }
}
