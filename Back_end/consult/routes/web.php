<?php

use Illuminate\Support\Facades\Route;
use App\Models;
use App\Models\Consult_Type;
use App\Models\Consultation;

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

Route::get(
    '/consulttypes',
    fn(Consultation $consultation) => view('consutltation', [
        'consulttypes' => Consult_Type::all()
    ])
);