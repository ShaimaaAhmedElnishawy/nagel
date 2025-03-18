<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\AdminController;



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
Route::post('/admin/login',[AuthController::class,'AdminLogin']);


    // ***Doctores***:-

    Route::middleware(['doctor'])->group(function () {
        
        Route::get('/doctor/info',[DoctorController::class,'show']);

        Route::put('/doctor/editName',[DoctorController::class,'editName']);
        Route::put('/doctor/editEmail',[DoctorController::class,'editEmail']);
        Route::put('/doctor/editPassword',[DoctorController::class,'editPassword']);
        Route::put('/doctor/editPhone',[DoctorController::class,'editPhone']);
        
        //***Clinics***:-
        Route::post('/doctor/AddClinic',[ClinicController::class,'AddClinic']);
        Route::get('/doctor/DisplayClinics',[ClinicController::class,'DisplayClinics']);
        Route::put('/doctor/EditClinic/{id}',[ClinicController::class,'EditClinic']);
        Route::delete('/doctor/DeleteClinic/{id}',[ClinicController::class,'DeleteClinic']);
        Route::post('/doctor/AddAvailableHours',[ClinicController::class,'AddAvailableHours']);
        Route::get('/doctor/DisplayAvailableHours',[ClinicController::class,'DisplayAvailableHours']);
        Route::put('/doctor/EditAvailableHours/{id}',[ClinicController::class,'EditAvailableHours']);
        Route::delete('/doctor/DeleteAvailableHours/{id}',[ClinicController::class,'DeleteAvailableHours']);

        Route::post('/doctor/logout',[AuthController::class,'DoctorLogout']);
    });

    //***Patients***:-
    
        Route::middleware(['patient'])->group(function () {
            Route::post('/patient/uploadNailImage',[PatientController::class,'uploadNailImage']);

           Route::get('/patient/showHistory',[PatientController::class,'showHistory']);

            Route::put('/patient/editName',[PatientController::class,'editName']);
            Route::put('/patient/editEmail',[PatientController::class,'editEmail']);
            Route::put('/patient/editPassword',[PatientController::class,'editPassword']);
            Route::put('/patient/editPhone',[PatientController::class,'editPhone']);
            
            Route::get('/patient/showDoctors',[PatientController::class,'showDoctorData']);
            Route::post('/doctors/{doctor}/rate',[PatientController::class,'rateDoctor']);

            //serach in doctors
            Route::post('/doctors/searchByName',[PatientController::class,'searchByName']);
            Route::post('/doctors/searchByAddress',[PatientController::class,'searchByAddress']);
            Route::post('/doctors/filterBySpecialization',[PatientController::class,'filterDoctorsBySpecialization']);
            
            Route::post('/patient/logout',[AuthController::class,'PatientLogout']);
            });
    
  //***Admin***:-

  Route::middleware(['admin'])->group(function () {

    Route::get('/admin/showPendedDoctors',[AdminController::class,'showPendedDoctors']);
    Route::post('/admin/approveDoctor/{doctor_id}',[AdminController::class,'approveDoctor']);
    Route::post('/admin/rejectDoctor/{doctor_id}',[AdminController::class,'rejectDoctor']);
  });

    
            

