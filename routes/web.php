<?php

use Illuminate\Support\Str;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () {
    return Str::random(32);
});

$router->get('/me', ['middleware' => 'auth.jwt', 'uses' => 'AuthController@me']);
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
});

$router->group(['prefix' => 'posts'], function () use ($router) {
    $router->get('all', 'PostController@all');
    $router->get('find/{id}', 'PostController@find');
    $router->post('store', ['middleware' => 'auth.jwt', 'uses' => 'PostController@store']);
    $router->put('update/{id}', ['middleware' => 'auth.jwt', 'uses' => 'PostController@update']);
    $router->delete('destroy/{id}', ['middleware' => 'auth.jwt', 'uses' => 'PostController@destroy']);
});
