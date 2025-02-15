<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//***Authentication*** :-
Route::post('/patient/register',[AuthController::class,'PatientRegister']);
Route::post('/patient/login',[AuthController::class,'PatientLogin']);
Route::post('/doctor/register',[AuthController::class,'DoctorRegister']);
Route::post('/doctor/login',[AuthController::class,'DoctorLogin']);


// ***Doctores***:-

 Route::middleware(['doctor'])->group(function () {
     
     Route::get('/doctor/info/{id}',[DoctorController::class,'show']);

     Route::put('/doctor/editName/{id}',[DoctorController::class,'editName']);
     Route::put('/doctor/editEmail/{id}',[DoctorController::class,'editEmail']);
     Route::put('/doctor/editPassword/{id}',[DoctorController::class,'editPassword']);
     Route::put('/doctor/editPhone/{id}',[DoctorController::class,'editPhone']);

     Route::post('/doctor/logout',[AuthController::class,'DoctorLogout']);
 });

 //***Patients***:-

 Route::middleware(['patient'])->group(function () {
     Route::post('/patient/uploadNailImage/{id}',[PatientController::class,'uploadNailImage']);

     Route::put('/patient/editName/{id}',[PatientController::class,'editName']);
     Route::put('/patient/editEmail/{id}',[PatientController::class,'editEmail']);
     Route::put('/patient/editPassword/{id}',[PatientController::class,'editPassword']);
     Route::put('/patient/editPhone/{id}',[PatientController::class,'editPhone']);
     
     
     Route::post('/patient/logout',[AuthController::class,'PatientLogout']);
    });
    
    Route::get('/patient/showDoctors',[PatientController::class,'showDoctorData']);
