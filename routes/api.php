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


//Rutas solo accesibles si la cuenta está verificada
Route::group(['middleware' => ['verified']], function () {
    //Recuperación de contraseña
    Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail');
    //Rutas de médico y paciente
    Route::group(['middleware' => 'auth:api'], function() {
        //Cerrar la sesión activa
        Route::post('logout','AuthController@logout');

        //Rutas del usuario
        Route::get('user', 'AuthController@user');
        Route::get('user/{code}', 'UserController@show');
        Route::put('user','UserController@update');
        Route::delete('user','UserController@destroy');

        //Obtener todas las recetas [medico o paciente]
        Route::get('prescription','PrescriptionController@index');

        //Contactos
        Route::get('contact','ContactController@index');
        Route::post('contact','ContactController@store');
        Route::put('contact','ContactController@update');
        Route::delete('contact/{contact}','ContactController@destroy');
        
        //Citas
        Route::get('appointment','AppointmentController@index');

        //Alarmas
        Route::get('alarm','AlarmController@index');
        Route::get('alarm/off/{alarm}','AlarmController@turnOff');
        Route::post('alarm','AlarmController@store');
        Route::delete('alarm/{alarm}','AlarmController@destroy');
        //Rutas del médico
        Route::group(['middleware' => ['role:Medic']], function () {
            //Rutas de recetas
            Route::post('prescription','PrescriptionController@store');
            Route::put('prescription','PrescriptionController@update');
            Route::delete('prescription/{prescription}','PrescriptionController@destroy');
            //Rutas de medicamento
            Route::get('medicament','MedicamentController@index');
            Route::get('medicament/{query}','MedicamentController@show');
            Route::post('medicament','MedicamentController@store');
            Route::put('medicament','MedicamentController@update');
            Route::delete('medicament/{medicament}','MedicamentController@destroy');
            //Citas
            Route::post('appointment','AppointmentController@store');
            Route::put('appointment','AppointmentController@update');
            Route::delete('appointment/{appointment}','AppointmentController@destroy');
            //Pacientes
            Route::get('patient','PatientController@index');
            Route::get('patient/{patient}','PatientController@show');
            Route::post('patient','PatientController@store');
            Route::put('patient','PatientController@update');
            Route::delete('patient/{patient}','PatientController@destroy');

        });
    });
});
