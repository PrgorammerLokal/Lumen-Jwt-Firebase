<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () {
    return Str::random(32);
});

$router->get('/mail/send', 'MailController@send');

$router->get('/tes', ['middleware' => ['auth.jwt', 'role:admin'], 'uses' => 'AuthController@tes']);

$router->get('/me', ['middleware' => 'auth.jwt', 'uses' => 'AuthController@me']);
// routes auth
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
    $router->post('reset/sendmail', 'AuthController@sendmail');
    $router->post('reset/password', ['middleware' => 'token', 'uses' => 'AuthController@reset']);
});
// route posts
$router->group(['prefix' => 'posts'], function () use ($router) {
    $router->get('all', 'PostController@all');
    $router->get('find/{id}', 'PostController@find');
    $router->post('store', ['middleware' => ['auth.jwt', 'role:admin'], 'uses' => 'PostController@store']);
    $router->put('update/{id}', ['middleware' => ['auth.jwt', 'role:admin'], 'uses' => 'PostController@update']);
    $router->delete('destroy/{id}', ['middleware' => ['auth.jwt', 'role:admin'], 'uses' => 'PostController@destroy']);
});
