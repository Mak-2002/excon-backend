<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\ConsultTypesController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\sessionsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//registerAPIs
Route::post('register' ,  [RegisterController::class, 'create']);


//sessions APIs
Route::post('login' ,  [sessionsController::class, 'login']);
Route::post('logout' ,  [SessionsController::class, 'logout']);

//experts APIs
Route::get('experts' ,  [ExpertsController::class, 'index']);
Route::get('experts/{expert}' ,  [ExpertsController::class, 'show']);
Route::get('expertsSchedule' ,  [ExpertsController::class, 'schedule']);
