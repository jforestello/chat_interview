<?php

use Illuminate\Routing\Router;

/** @var $router Router */

$router->middleware(["guest"])->group(function (Router $router) {
    $router->get('/', ['name' => 'guest.login_form', 'uses' => 'App\Controllers\LoginController@login']);
    $router->get('register', ['name' => 'guest.register_form', 'uses' => 'App\Controllers\LoginController@register']);
    $router->post('/', ['name' => 'guest.login', 'uses' => 'App\Controllers\UsersController@login']);
    $router->post('register', ['name' => 'guest.register', 'uses' => 'App\Controllers\UsersController@register']);
});

$router->middleware(["logged"])->group(function (Router $router) {
    $router->get('list', ['name' => 'logged.list', 'uses' => 'App\Controllers\ChatController@index']);
    $router->get('chat/{id}', ['name' => 'logged.chat_detail', 'uses' => 'App\Controllers\ChatController@detail']);
    $router->post('chat', ['name' => 'logged.chat_create', 'uses' => 'App\Controllers\ChatController@create']);
    $router->put('chat/{id}', ['name' => 'logged.send_message', 'uses' => 'App\Controllers\ChatController@send']);
    $router->get('chat/{id}/update', ['name' => 'logged.fetch_messages', 'uses' => 'App\Controllers\ChatController@fetchNew']);
    $router->post('logout', ['name' => 'logged.logout', 'uses' => 'App\Controllers\UsersController@logout']);
});

// catch-all route
$router->any('{any}', "App\Controllers\Controller@getNotFoundView")->where('any', '(.*)');
