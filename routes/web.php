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
    // return $app->version();  
    echo 'API SACSP_V1'  ;
});

$app->post('/login', 'AuthController@postLogin');

$app->group(['middleware'=>'auth'], function($app){
    // $app->get('/api_sacsp/eventos_sala/{parm_sala}/{parm_fecha}', 'EventosController@eventos_sala');
    $app->put('/reservar_evento', 'EventosController@reservar_evento');     
});
