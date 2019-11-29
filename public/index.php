<?php

/**
 * Illuminate/Routing
 *
 * @source https://github.com/illuminate/routing
 */

define("DOCUMENT_ROOT", dirname(__FILE__, 2));
define("DIR_SEPARATOR", "\\");

require_once '../vendor/autoload.php';

use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use App\repositories\UserRepository;
use App\repositories\ChatChannelRepository;
use App\repositories\MessageRepository;
use Illuminate\Container\Container;
use App\database\Database;
use \Illuminate\Routing\Pipeline;
use \App\middlewares\StartSession;

/**
 * Loads the container and the database from the cache
 * @var Container $container
 * @var Database $database
 */
[$container] = require_once '../bootstrap/autoload.php';

$database = new Database;

$sessionStarter = new StartSession();
// Create a request from server variables, and bind it to the container; optional
$request = Request::capture();

$container->instance('Illuminate\Http\Request', $request);
$container->instance('App\repositories\UserRepository', new UserRepository($database));
$container->instance('App\repositories\MessageRepository', new MessageRepository($database));
$container->instance('App\repositories\ChatChannelRepository', new ChatChannelRepository($database));

// Using Illuminate/Events/Dispatcher here (not required); any implementation of
// Illuminate/Contracts/Event/Dispatcher is acceptable
$events = new Dispatcher($container);

// Create the router instance
$router = new Router($events, $container);

// Routes middlewares
$routerMiddle = [
    'logged' => \App\middlewares\Logged::class,
    'guest' => \App\middlewares\Guest::class
];

// Load the middlewares
foreach ($routerMiddle as $key => $middle) {
    $router->aliasMiddleware($key, $middle);
}

// Load the routes
require_once '../src/routes/routes.php';

// Create the redirect instance
$redirect = new Redirector(new UrlGenerator($router->getRoutes(), $request));

require_once '../src/helpers.php';

$container->instance('Illuminate\Routing\Redirector', $redirect);
try {
    // Dispatch the request through the router
    $response = (new Pipeline())
        ->send($request)
        ->through($sessionStarter)
        ->then(function ($request) use ($router) {
            return $router->dispatch($request);
        });
} catch (Exception $e) {
    echo "<pre> Error code: {$e->getCode()}\n Error Message: {$e->getMessage()}";
    die;
}

// Send the response back to the browser
$response->send();
