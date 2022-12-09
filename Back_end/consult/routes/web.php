<?php

use Illuminate\Support\Facades\Route;
use App\Models\Consultation;
use App\Models\ConsultType;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/consulttypes', function () {
    return view('consultations', [
        'types' => ConsultType::with('consultations')->get()
    ]);
});