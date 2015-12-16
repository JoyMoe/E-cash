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
    return $app->welcome();
});

$app->post('/api/order', 'ApiController@submitOrder');
$app->get('/api/order/{order_id}', 'ApiController@getOrder');
$app->put('/api/order/{order_id}', 'ApiController@modifyOrder');
$app->post('/api/order/{order_id}', 'ApiController@completeOrder');
$app->delete('/api/order/{order_id}', 'ApiController@removeOrder');

$app->get('/order/{order_id}', 'OrderController@showOrder');
$app->post('/order/{order_id}', 'OrderController@doOrder');

$app->get('/order/{order_id}/back/{gateway}', [
    'as' => 'back', 'uses' => 'OrderController@doBack'
]);
$app->post('/order/{order_id}/back/{gateway}', 'OrderController@doBack');
