***REMOVED***

***REMOVED***
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
***REMOVED***

$app->get('/', function () use ($app) ***REMOVED***
    return $app->welcome(***REMOVED***
***REMOVED******REMOVED***

$app->post('/api/order', 'ApiController@submitOrder'***REMOVED***
$app->get('/api/order***REMOVED*****REMOVED***order_id***REMOVED***', 'ApiController@getOrder'***REMOVED***
$app->put('/api/order***REMOVED*****REMOVED***order_id***REMOVED***', 'ApiController@modifyOrder'***REMOVED***
$app->post('/api/order***REMOVED*****REMOVED***order_id***REMOVED***', 'ApiController@completeOrder'***REMOVED***
$app->delete('/api/order***REMOVED*****REMOVED***order_id***REMOVED***', 'ApiController@removeOrder'***REMOVED***

$app->get('/order***REMOVED*****REMOVED***order_id***REMOVED***', 'OrderController@showOrder'***REMOVED***
$app->post('/order***REMOVED*****REMOVED***order_id***REMOVED***', 'OrderController@doOrder'***REMOVED***

$app->get('/order***REMOVED*****REMOVED***order_id***REMOVED*****REMOVED***back***REMOVED*****REMOVED***gateway***REMOVED***', [
    'as' => 'back', 'uses' => 'OrderController@doBack'
]***REMOVED***
$app->post('/order***REMOVED*****REMOVED***order_id***REMOVED*****REMOVED***back***REMOVED*****REMOVED***gateway***REMOVED***', 'OrderController@doBack'***REMOVED***
