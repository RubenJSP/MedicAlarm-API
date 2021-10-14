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

/*Route::middleware('auth:passport')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('login', 'AuthController@login');
Route::post('register','UserController@store');

Route::group(['middleware' => 'auth:api'], function() {
    Route::get('getUser', 'AuthController@user');
    Route::get('getPrescriptions','PrescriptionController@index');
    Route::post('logout','AuthController@logout');
    Route::group(['middleware' => ['role:Medic']], function () {
        Route::get('users','UserController@index');
        Route::post('deleteUser','UserController@destroy');
        Route::post('updateUser','UserController@update');
        Route::post('storePrescription','PrescriptionController@store');
        Route::post('updatePrescription','PrescriptionController@update');
        Route::post('deletePrescription','PrescriptionController@destroy');
    });
});
