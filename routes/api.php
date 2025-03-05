<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AIController;


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
     
     Route::get('/doctor/info',[DoctorController::class,'show']);

     Route::put('/doctor/editName',[DoctorController::class,'editName']);
     Route::put('/doctor/editEmail',[DoctorController::class,'editEmail']);
     Route::put('/doctor/editPassword',[DoctorController::class,'editPassword']);
     Route::put('/doctor/editPhone',[DoctorController::class,'editPhone']);

     Route::post('/doctor/logout',[AuthController::class,'DoctorLogout']);
 });

 //***Patients***:-
 
 Route::middleware(['patient'])->group(function () {
     Route::post('/patient/uploadNailImage',[PatientController::class,'uploadNailImage']);
     Route::post('/patient/diagnose/{imageId}', [AIController::class, 'diagnose']);

     Route::put('/patient/editName',[PatientController::class,'editName']);
     Route::put('/patient/editEmail',[PatientController::class,'editEmail']);
     Route::put('/patient/editPassword',[PatientController::class,'editPassword']);
     Route::put('/patient/editPhone',[PatientController::class,'editPhone']);
     
     Route::get('/patient/showDoctors',[PatientController::class,'showDoctorData']);
     
     Route::post('/patient/logout',[AuthController::class,'PatientLogout']);
    });
    
