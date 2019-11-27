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

/**
 * Loads the container and the database from the cache
 * @var Container $container
 * @var Database $database
 */
[$container, $database] = require_once '../bootstrap/autoload.php';

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

// Load the routes
require_once '../src/routes/routes.php';

// Create the redirect instance
$redirect = new Redirector(new UrlGenerator($router->getRoutes(), $request));

// use redirect
// return $redirect->home();
// return $redirect->back();
// return $redirect->to('/');

try {
    // Dispatch the request through the router
    $response = $router->dispatch($request);
} catch (Exception $e) {
    echo "<pre> Error code: {$e->getCode()}\n Error Message: {$e->getMessage()}";
    die;
}

// Send the response back to the browser
$response->send();
