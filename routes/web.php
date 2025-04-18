<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $path = "public/$folder/$filename";

    if (!Storage::exists($path)) {
        abort(404);
    }

    $file = Storage::get($path);
    $mimeType = Storage::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $mimeType);
});
