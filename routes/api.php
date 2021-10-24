<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Rutas públicas
Route::post('register','UserController@store');
Route::post('login', 'AuthController@login');
Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail');

//Rutas solo accesibles si la cuenta está verificada
Route::group(['middleware' => ['verified']], function () {
    //Rutas de médico y paciente
    Route::group(['middleware' => 'auth:api'], function() {
        //Cerrar la sesión activa
        Route::post('logout','AuthController@logout');

        //Rutas del usuario
        Route::get('user', 'AuthController@user');
        Route::put('user','UserController@update');
        Route::delete('user','UserController@destroy');

        //Obtener todas las recetas [medico o paciente]
        Route::get('prescription','PrescriptionController@index');

        //Contactos
        Route::get('contact','ContactController@index');
        Route::post('contact','ContactController@store');
        Route::put('contact','ContactController@update');
        Route::delete('contact/{contact}','ContactController@destroy');

        //Rutas del médico
        Route::group(['middleware' => ['role:Medic']], function () {
            //Rutas de recetas
            Route::post('prescription','PrescriptionController@store');
            Route::put('prescription','PrescriptionController@update');
            Route::delete('prescription','PrescriptionController@destroy');
        });
    });
});
