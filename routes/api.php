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
        Route::get('getUser', 'AuthController@user');
        Route::get('getPrescriptions','PrescriptionController@index');
        Route::post('logout','AuthController@logout');
        Route::post('updateUser','UserController@update');
        Route::get('users','UserController@index');
        Route::post('deleteUser','UserController@destroy');
        //Rutas del médico
        Route::group(['middleware' => ['role:Medic']], function () {
            Route::post('storePrescription','PrescriptionController@store');
            Route::post('updatePrescription','PrescriptionController@update');
            Route::post('deletePrescription','PrescriptionController@destroy');
        });
    });
});
