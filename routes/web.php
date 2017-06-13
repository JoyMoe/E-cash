<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'api'], function () use ($app) {
    $app->post('orders', 'ApiController@store');
    $app->get('orders/{id}', 'ApiController@show');
    $app->put('orders/{id}', 'ApiController@update');
    $app->patch('orders/{id}', 'ApiController@update');
    $app->post('orders/{id}', 'ApiController@complete');
    $app->delete('orders/{id}', 'ApiController@destroy');
});

$app->get('/orders/{id}', 'OrderController@show');
$app->post('/orders/{id}', 'OrderController@pay');

$app->get('/orders/{id}/callback/{gateway}', [
    'as' => 'callback', 'uses' => 'OrderController@callback',
]);
$app->post('/orders/{id}/callback/{gateway}', 'OrderController@callback');