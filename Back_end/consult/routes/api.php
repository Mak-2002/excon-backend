<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\ConsultTypesController;
use App\Http\Controllers\ExpertsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\sessionsController;
use App\Http\Controllers\UsersController;
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

//sessions routes
Route::post('register' ,  [sessionsController::class, 'create']);
Route::post('login' ,  [sessionsController::class, 'login'])->name('login');
Route::post('logout' ,  [sessionsController::class, 'logout']);

//experts routes
Route::get('experts' ,  [ExpertsController::class, 'index']);
Route::get('expert/{expert}' ,  [ExpertsController::class, 'show']);
Route::post('expert/updaterating' ,  [ExpertsController::class, 'update_rating']);
Route::get('expertsSchedule' ,  [ExpertsController::class, 'schedule']);

//users routes
Route::get('user/favorites', [UsersController::class, 'favorites']);
Route::post('user/add_favorite', [UsersController::class, 'add_favorite']);
Route::post('pay', [UsersController::class, 'pay']);