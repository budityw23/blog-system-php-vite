<?php
use DI\Container;
use DI\Bridge\Slim\Bridge;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Create Container
$container = new Container();

// Set up dependencies
$container->set('db', function() {
    $config = require __DIR__ . '/../config/database.php';
    try {
        return new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    } catch (\PDOException $e) {
        throw new \Exception("Database connection failed: " . $e->getMessage());
    }
});

// Register controllers
$container->set(App\Controllers\AuthController::class, function($c) {
    return new App\Controllers\AuthController(new App\Models\User($c->get('db')));
});

$container->set(App\Controllers\PostController::class, function($c) {
    return new App\Controllers\PostController(new App\Models\Post($c->get('db')));
});

$container->set(App\Controllers\UserController::class, function($c) {
    return new App\Controllers\UserController(new App\Models\User($c->get('db')));
});

// Create app
$app = Bridge::create($container);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add routes
require __DIR__ . '/../src/Routes/api.php';

$app->run();
