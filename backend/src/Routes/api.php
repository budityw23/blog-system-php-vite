<?php

use Slim\Routing\RouteCollectorProxy;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\UserController;
use App\Middleware\JwtMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonBodyParserMiddleware;

// Add global middleware
$app->add(new CorsMiddleware());
$app->add(new JsonBodyParserMiddleware());

// Define routes
$app->group('/api', function (RouteCollectorProxy $group) {
    // Public routes

    $group->post('/login', [AuthController::class, 'login']);
    $group->get('/posts', [PostController::class, 'getAllPosts']);
    
    // Protected routes
    $group->group('/admin', function (RouteCollectorProxy $group) {
        // User management
        $group->group('/users', function (RouteCollectorProxy $group) {
            $group->get('', [UserController::class, 'getAllUsers']);
            $group->post('', [UserController::class, 'createUser']);
            $group->put('/{id}', [UserController::class, 'updateRole']);
            $group->delete('/{id}', [UserController::class, 'deleteUser']);
        })->add(new JwtMiddleware(['admin']));
    });

    // Content management
    $group->group('/content', function (RouteCollectorProxy $group) {
        $group->post('/posts', [PostController::class, 'createPost']);
        $group->put('/{id}', [PostController::class, 'updatePost']);
        $group->delete('/{id}', [PostController::class, 'deletePost']);
    })->add(new JwtMiddleware(['editor']));
});

// Handle CORS preflight requests
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// Handle 404
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});

// Error handling
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $payload = [
        'error' => [
            'message' => $exception->getMessage()
        ]
    ];

    if ($displayErrorDetails) {
        $payload['error']['trace'] = $exception->getTrace();
    }

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));

    $status = 500;
    if ($exception instanceof HttpNotFoundException) {
        $status = 404;
    }

    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
});
